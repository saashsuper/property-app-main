<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\BlockIssue;
use App\Models\BlockType;
use App\Models\BlockUnit;
use App\Models\BlockWorkOrder;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Show the dashboard
     */
    public function index()
    {
        $user = auth()->user();
        $isContractorAdmin = $user->hasType('Contractor Admin');

        if ($isContractorAdmin) {
            // Contractor Admin specific data
            $stats = [
                'total_contractor_users' => User::whereHas('userType', function($query) {
                    $query->where('name', 'Contractor User');
                })->count(),
                'total_work_orders' => BlockWorkOrder::count(),
                'assigned_work_orders' => BlockWorkOrder::where('contractor_id', $user->id)->count(),
                'completed_work_orders' => BlockWorkOrder::where('contractor_id', $user->id)
                    ->where('status', 3)->count(), // Status 3 is Completed
            ];

            // Get recent contractor users created by this admin
            $recentContractorUsers = User::whereHas('userType', function($query) {
                $query->where('name', 'Contractor User');
            })->where('created_by', $user->id)
            ->latest()
            ->take(5)
            ->get();

            // Get recent block work orders assigned to this admin
            $recentWorkOrders = BlockWorkOrder::where('contractor_id', $user->id)
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard.index', compact('stats', 'recentContractorUsers', 'recentWorkOrders', 'isContractorAdmin'));
        } else {
            // Regular admin/other users - show all data
            $stats = [
                'total_blocks' => Block::count(),
                'total_block_types' => BlockType::count(),
                'total_units' => BlockUnit::count(),
                'total_issues' => BlockIssue::count(),
                'open_issues' => BlockIssue::where('issue_status_id', 1)->count(),
                'in_progress_issues' => BlockIssue::where('issue_status_id', 2)->count(),
                'resolved_issues' => BlockIssue::where('issue_status_id', 3)->count(),
                'high_priority_issues' => BlockIssue::where('priority_id', '>=', 3)->count(),
            ];

            // Get recent data for widgets
            $recentBlocks = Block::with('blockType')
                ->latest()
                ->take(5)
                ->get();

            $recentIssues = BlockIssue::with('block')
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard.index', compact('stats', 'recentBlocks', 'recentIssues', 'isContractorAdmin'));
        }
    }

    /**
     * Get issues statistics for charts
     */
    public function getIssuesStats(): JsonResponse
    {
        $stats = [
            'by_status' => [
                'Open' => BlockIssue::where('issue_status_id', 1)->count(),
                'In Progress' => BlockIssue::where('issue_status_id', 2)->count(),
                'Resolved' => BlockIssue::where('issue_status_id', 3)->count(),
                'Closed' => BlockIssue::where('issue_status_id', 4)->count(),
                'On Hold' => BlockIssue::where('issue_status_id', 5)->count(),
            ],
            'by_priority' => [
                'Low' => BlockIssue::where('priority_id', 1)->count(),
                'Normal' => BlockIssue::where('priority_id', 2)->count(),
                'High' => BlockIssue::where('priority_id', 3)->count(),
                'Urgent' => BlockIssue::where('priority_id', 4)->count(),
                'Critical' => BlockIssue::where('priority_id', 5)->count(),
            ],
            'by_block' => Block::withCount('issues')
                ->having('issues_count', '>', 0)
                ->orderBy('issues_count', 'desc')
                ->take(6)
                ->get()
                ->map(function ($block) {
                    return [
                        'name' => $block->name,
                        'count' => $block->issues_count
                    ];
                }),
            'trend' => $this->getIssuesTrend(),
        ];

        return response()->json($stats);
    }

    /**
     * Get issues trend data for the last 30 days
     */
    private function getIssuesTrend(): array
    {
        $trendData = [];
        $dates = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dates[] = $date->format('M d');
            
            $trendData['dates'] = $dates;
            $trendData['Open'][] = BlockIssue::where('issue_status_id', 1)
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->count();
            $trendData['In Progress'][] = BlockIssue::where('issue_status_id', 2)
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->count();
            $trendData['Resolved'][] = BlockIssue::where('issue_status_id', 3)
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->count();
            $trendData['Closed'][] = BlockIssue::where('issue_status_id', 4)
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->count();
        }

        return $trendData;
    }

    /**
     * Get dashboard summary statistics
     */
    public function getDashboardStats(): JsonResponse
    {
        $stats = [
            'total_blocks' => Block::count(),
            'total_block_types' => BlockType::count(),
            'total_units' => BlockUnit::count(),
            'total_issues' => BlockIssue::count(),
            'open_issues' => BlockIssue::where('issue_status_id', 1)->count(),
            'in_progress_issues' => BlockIssue::where('issue_status_id', 2)->count(),
            'resolved_issues' => BlockIssue::where('issue_status_id', 3)->count(),
            'high_priority_issues' => BlockIssue::where('priority_id', '>=', 3)->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get recent issues for dashboard widget
     */
    public function getRecentIssues(): JsonResponse
    {
        $recentIssues = BlockIssue::with(['block', 'unit'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($issue) {
                return [
                    'id' => $issue->id,
                    'ref_no' => $issue->ref_no,
                    'issue' => $issue->issue ?? 'No title',
                    'block_name' => $issue->block->name ?? 'N/A',
                    'status' => $issue->status_text,
                    'status_color' => $issue->status_color,
                    'priority' => $issue->priority_text,
                    'priority_color' => $issue->priority_color,
                    'created_at' => $issue->created_at->diffForHumans(),
                ];
            });

        return response()->json($recentIssues);
    }

    /**
     * Get recent blocks for dashboard widget
     */
    public function getRecentBlocks(): JsonResponse
    {
        $recentBlocks = Block::with('blockType')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($block) {
                return [
                    'id' => $block->id,
                    'name' => $block->name,
                    'type' => $block->blockType->name ?? 'N/A',
                    'created_at' => $block->created_at->diffForHumans(),
                ];
            });

        return response()->json($recentBlocks);
    }
}
