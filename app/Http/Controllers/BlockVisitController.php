<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\BlockVisit;
use App\Models\JobReason;
use App\Models\JobStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlockVisitController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'block_id' => 'required|exists:blocks,id',
            'user_id' => 'required|exists:users,id',
            'scheduled_date_time' => 'required|date',
            'job_reason_id' => 'required|exists:job_reasons,id',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $blockVisit = BlockVisit::create([
                'block_id' => $request->block_id,
                'ref_no' => 'SV-' . strtoupper(Str::random(6)),
                'scheduled_date_time' => $request->scheduled_date_time,
                'job_reason_id' => $request->job_reason_id,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Create team member (user assigned to the visit)
            $blockVisit->team()->create([
                'user_id' => $request->user_id,
                'leed' => true, // Set as lead team member
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Site visit scheduled successfully',
                'data' => $blockVisit->load('jobReason', 'createdByUser', 'team')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error scheduling site visit: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(BlockVisit $blockVisit)
    {
        try {
            $blockVisit->load(['jobReason', 'jobStatus', 'createdByUser', 'updatedByUser', 'team.user']);
            
            return response()->json([
                'success' => true,
                'data' => $blockVisit
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not fetch site visit details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, BlockVisit $blockVisit)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'scheduled_date_time' => 'required|date',
            'job_reason_id' => 'required|exists:job_reasons,id',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $blockVisit->update([
                'scheduled_date_time' => $request->scheduled_date_time,
                'job_reason_id' => $request->job_reason_id,
                'notes' => $request->notes,
                'updated_by' => auth()->id(),
            ]);

            // Update team member (user assigned to the visit)
            // First, remove existing team members
            $blockVisit->team()->delete();
            
            // Add the new team member
            $blockVisit->team()->create([
                'user_id' => $request->user_id,
                'leed' => true, // Set as lead team member
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Site visit updated successfully',
                'data' => $blockVisit->load('jobReason', 'updatedByUser', 'team')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating site visit: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(BlockVisit $blockVisit)
    {
        try {
            $blockVisit->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Site visit deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting site visit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get block visits for a specific block
     */
    public function getBlockVisits($blockId)
    {
        try {
            $visits = BlockVisit::where('block_id', $blockId)
                ->with(['jobReason', 'jobStatus', 'team.user'])
                ->orderBy('scheduled_date_time', 'desc')
                ->get()
                ->map(function($visit) {
                    return [
                        'id' => $visit->id,
                        'ref_no' => $visit->ref_no,
                        'scheduled_date_time' => $visit->scheduled_date_time,
                        'job_reason_name' => $visit->jobReason->name ?? 'N/A',
                        'job_status_name' => $visit->jobStatus->name ?? 'N/A',
                        'user_name' => $visit->team->first()->user->name ?? 'N/A',
                        'notes' => $visit->notes,
                        'created_at' => $visit->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $visits
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching block visits: ' . $e->getMessage()
            ], 500);
        }
    }
}


