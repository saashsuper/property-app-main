<?php

namespace App\Services;

use App\Models\BlockWorkOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WorkDocketService
{
    /**
     * Generate and save work docket PDF for a completed work order
     *
     * @param BlockWorkOrder $workOrder
     * @return array|null Returns array with 'path' and 'name' on success, null on failure
     */
    public function generateWorkDocket(BlockWorkOrder $workOrder): ?array
    {
        try {
            // Load all necessary relationships
            $workOrder->load([
                'block',
                'blockIssue.images',
                'blockIssue.priority',
                'blockIssue.issueStatus',
                'blockIssue.issueType',
                'blockIssue.reportedBy',
                'blockIssue.assignedTo',
                'blockIssue.blockUnit',
                'blockIssue.blockBuilding',
                'blockUnit',
                'blockBuilding',
                'priority',
                'jobStatus',
                'contractor',
                'contractCompany',
                'issuedBy',
                'creator',
                'images',
                'notes.creator',
                'teamMembers.user',
            ]);

            // Generate PDF
            $pdf = Pdf::loadView('work-dockets.work-docket', [
                'workOrder' => $workOrder,
            ]);

            // Set PDF options
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption('enable-local-file-access', true);

            // Delete old PDF if it exists (for regeneration)
            if ($workOrder->pdf_path && $workOrder->pdf_name) {
                $oldFullPath = storage_path('app/public/' . $workOrder->pdf_path . '/' . $workOrder->pdf_name);
                if (file_exists($oldFullPath)) {
                    Storage::delete('public/' . $workOrder->pdf_path . '/' . $workOrder->pdf_name);
                }
            }

            // Generate unique filename
            $pdfName = 'work-docket-' . $workOrder->ref_no . '-' . now()->format('Y-m-d-His') . '.pdf';
            $pdfPath = 'work-orders/dockets';

            // Ensure directory exists
            Storage::makeDirectory('public/' . $pdfPath);

            // Save PDF to storage
            $fullPath = storage_path('app/public/' . $pdfPath . '/' . $pdfName);
            $pdf->save($fullPath);

            // Save PDF path and name to database
            $workOrder->update([
                'pdf_path' => $pdfPath,
                'pdf_name' => $pdfName,
            ]);

            return [
                'path' => $pdfPath,
                'name' => $pdfName,
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to generate work docket PDF', [
                'work_order_id' => $workOrder->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Download work docket PDF
     *
     * @param BlockWorkOrder $workOrder
     * @return \Illuminate\Http\Response|null
     */
    public function downloadWorkDocket(BlockWorkOrder $workOrder)
    {
        if (!$workOrder->pdf_path || !$workOrder->pdf_name) {
            return null;
        }

        $filePath = storage_path('app/public/' . $workOrder->pdf_path . '/' . $workOrder->pdf_name);

        if (!file_exists($filePath)) {
            return null;
        }

        return response()->download($filePath, $workOrder->pdf_name, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}

