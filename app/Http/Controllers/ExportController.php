<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class ExportController extends Controller
{
    /**
     * Export data to PDF
     */
    public function exportPdf(Request $request, $type)
    {
        $data = $this->getExportData($request, $type);
        $view = $this->getPdfView($type);
        $title = $this->getExportTitle($type);

        $pdf = Pdf::loadView($view, [
            'data' => $data,
            'title' => $title,
            'exported_at' => now()->format('M d, Y H:i:s'),
        ]);

        return $pdf->download($title . '_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * Export data to Excel
     */
    public function exportExcel(Request $request, $type)
    {
        $data = $this->getExportData($request, $type);
        $title = $this->getExportTitle($type);

        return Excel::download(new class($data, $title) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle {
            private $data;
            private $title;

            public function __construct($data, $title) {
                $this->data = $data;
                $this->title = $title;
            }

            public function array(): array {
                return $this->data;
            }

            public function headings(): array {
                return array_keys($this->data[0] ?? []);
            }

            public function title(): string {
                return $this->title;
            }
        }, $title . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    /**
     * Export data to Print view
     */
    public function exportPrint(Request $request, $type)
    {
        $data = $this->getExportData($request, $type);
        $view = $this->getPrintView($type);
        $title = $this->getExportTitle($type);

        return view($view, [
            'data' => $data,
            'title' => $title,
            'exported_at' => now()->format('M d, Y H:i:s'),
        ]);
    }

    /**
     * Get export data based on type
     */
    private function getExportData(Request $request, $type)
    {
        switch ($type) {
            case 'blocks':
                return $this->getBlocksData($request);
            case 'block-types':
                return $this->getBlockTypesData($request);
            case 'block-issues':
                return $this->getBlockIssuesData($request);
            case 'work-orders':
                return $this->getWorkOrdersData($request);
            case 'block-work-orders':
                return $this->getBlockWorkOrdersData($request);
            case 'issues':
                return $this->getIssuesData($request);
            case 'block-visits':
                return $this->getBlockVisitsData($request);
                    case 'block-inspections':
            return $this->getBlockInspectionsData($request);
        case 'user-types':
            return $this->getUserTypesData($request);
        case 'users':
            return $this->getUsersData($request);
            default:
                return [];
        }
    }

    /**
     * Get PDF view name
     */
    private function getPdfView($type)
    {
        return 'exports.pdf.base';
    }

    /**
     * Get Print view name
     */
    private function getPrintView($type)
    {
        return 'exports.print.base';
    }

    /**
     * Get export title
     */
    private function getExportTitle($type)
    {
        $titles = [
            'blocks' => 'Blocks',
            'block-types' => 'Block Types',
            'block-issues' => 'Block Issues',
            'work-orders' => 'Work Orders',
            'block-work-orders' => 'Block Work Orders',
            'issues' => 'Issues',
            'block-visits' => 'Site Visits',
            'block-inspections' => 'Block Inspections',
            'user-types' => 'User Types',
            'users' => 'Users',
        ];

        return $titles[$type] ?? ucfirst(str_replace('-', ' ', $type));
    }

    /**
     * Get blocks data for export
     */
    private function getBlocksData(Request $request)
    {
        $query = \App\Models\Block::with(['blockType', 'creator'])->active();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('management_company', 'like', "%{$search}%")
                  ->orWhere('address1', 'like', "%{$search}%");
            });
        }

        $blocks = $query->orderBy('created_at', 'desc')->get();

        return $blocks->map(function ($block) {
            return [
                'ID' => $block->id,
                'Name' => $block->name,
                'Type' => $block->blockType->name ?? 'N/A',
                'Management Company' => $block->management_company,
                'Address' => $block->full_address,
                'Units' => $block->no_of_units ?? 0,
                'Car Spaces' => $block->car_spaces,
                'Created By' => $block->creator->name ?? 'System',
                'Created Date' => $block->created_at->format('M d, Y'),
            ];
        })->toArray();
    }

    /**
     * Get block types data for export
     */
    private function getBlockTypesData(Request $request)
    {
        $query = \App\Models\BlockType::withCount('blocks');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $blockTypes = $query->orderBy('created_at', 'desc')->get();

        return $blockTypes->map(function ($blockType) {
            return [
                'ID' => $blockType->id,
                'Name' => $blockType->name,
                'Blocks Count' => $blockType->blocks_count,
                'Created Date' => $blockType->created_at->format('M d, Y'),
            ];
        })->toArray();
    }

    /**
     * Get block issues data for export
     */
    private function getBlockIssuesData(Request $request)
    {
        $query = \App\Models\BlockIssue::with(['block', 'reportedBy', 'assignedTo']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ref_no', 'like', "%{$search}%")
                  ->orWhere('issue', 'like', "%{$search}%");
            });
        }

        $issues = $query->orderBy('created_at', 'desc')->get();

        return $issues->map(function ($issue) {
            return [
                'ID' => $issue->id,
                'Reference' => $issue->ref_no,
                'Issue' => $issue->issue,
                'Block' => $issue->block->name ?? 'N/A',
                'Status' => $issue->status_text,
                'Priority' => $issue->priority_text,
                'Reported By' => $issue->reportedBy->name ?? 'N/A',
                'Assigned To' => $issue->assignedTo->name ?? 'Unassigned',
                'Created Date' => $issue->created_at->format('M d, Y'),
            ];
        })->toArray();
    }

    /**
     * Get work orders data for export
     */
    private function getWorkOrdersData(Request $request)
    {
        $query = \App\Models\WorkOrder::with(['user', 'creator'])->active();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('fault_description', 'like', "%{$search}%");
            });
        }

        $workOrders = $query->orderBy('created_at', 'desc')->get();

        return $workOrders->map(function ($workOrder) {
            return [
                'ID' => $workOrder->id,
                'Code' => $workOrder->code,
                'Fault Description' => $workOrder->fault_description,
                'Category' => $workOrder->issue_category,
                'Type' => $workOrder->issue_type,
                'Priority' => $workOrder->priority_label,
                'Status' => $workOrder->status_text,
                'Contact Name' => $workOrder->contact_name ?? 'N/A',
                'Contact Email' => $workOrder->contact_email ?? 'N/A',
                'Deadline' => $workOrder->deadline,
                'Created By' => $workOrder->creator->name ?? 'System',
                'Created Date' => $workOrder->created_at->format('M d, Y'),
            ];
        })->toArray();
    }

    /**
     * Get block work orders data for export
     */
    private function getBlockWorkOrdersData(Request $request)
    {
        $query = \App\Models\BlockWorkOrder::with(['block', 'issuedBy'])->active();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ref_no', 'like', "%{$search}%")
                  ->orWhere('issue', 'like', "%{$search}%");
            });
        }

        $workOrders = $query->orderBy('created_at', 'desc')->get();

        return $workOrders->map(function ($workOrder) {
            return [
                'ID' => $workOrder->id,
                'Reference' => $workOrder->ref_no,
                'Issue' => $workOrder->issue,
                'Block' => $workOrder->block->name ?? 'N/A',
                'Priority' => $workOrder->priority_text,
                'Status' => $workOrder->status_text,
                'Contact Name' => $workOrder->contact_name ?? 'N/A',
                'Contact Email' => $workOrder->contact_email ?? 'N/A',
                'Deadline' => $workOrder->deadline_date ? $workOrder->deadline_date->format('M d, Y') : 'N/A',
                'Issued By' => $workOrder->issuedBy->name ?? 'N/A',
                'Created Date' => $workOrder->created_at->format('M d, Y'),
            ];
        })->toArray();
    }

    /**
     * Get issues data for export
     */
    private function getIssuesData(Request $request)
    {
        $query = \App\Models\Issue::with(['reportedBy', 'assignedTo']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ref_no', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $issues = $query->orderBy('created_at', 'desc')->get();

        return $issues->map(function ($issue) {
            return [
                'ID' => $issue->id,
                'Reference' => $issue->ref_no,
                'Title' => $issue->title,
                'Category' => $issue->category,
                'Status' => $issue->status_text,
                'Priority' => $issue->priority_text,
                'Location' => $issue->location ?? 'N/A',
                'Reported By' => $issue->reportedBy->name ?? 'N/A',
                'Assigned To' => $issue->assignedTo->name ?? 'Unassigned',
                'Created Date' => $issue->created_at->format('M d, Y'),
            ];
        })->toArray();
    }

    /**
     * Get block visits data for export
     */
    private function getBlockVisitsData(Request $request)
    {
        $query = \App\Models\BlockVisit::with('block');

        $visits = $query->orderBy('created_at', 'desc')->get();

        return $visits->map(function ($visit) {
            return [
                'ID' => $visit->id,
                'Reference' => $visit->ref_no,
                'Block' => $visit->block->name ?? 'N/A',
                'Scheduled Date' => $visit->scheduled_date_time ? $visit->scheduled_date_time->format('M d, Y H:i') : 'N/A',
                'Start Time' => $visit->start_date_time ? $visit->start_date_time->format('M d, Y H:i') : 'N/A',
                'End Time' => $visit->end_date_time ? $visit->end_date_time->format('M d, Y H:i') : 'N/A',
                'Status' => $this->getVisitStatus($visit),
                'Notes' => $visit->notes ?? 'N/A',
                'Created Date' => $visit->created_at->format('M d, Y'),
            ];
        })->toArray();
    }

    /**
     * Get visit status
     */
    private function getVisitStatus($visit)
    {
        if ($visit->start_date_time && $visit->end_date_time) {
            return 'Completed';
        } elseif ($visit->start_date_time) {
            return 'In Progress';
        } else {
            return 'Scheduled';
        }
    }

    /**
     * Get block inspections data for export
     */
    private function getBlockInspectionsData(Request $request)
    {
        $query = \App\Models\BlockInspection::with(['block', 'creator', 'inspectionTeams.user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ref_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('block', function ($blockQuery) use ($search) {
                      $blockQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('job_status_id', $request->status);
        }

        $inspections = $query->orderBy('created_at', 'desc')->get();

        return $inspections->map(function ($inspection) {
            $leadInspector = $inspection->inspectionTeams->where('is_lead', true)->first();
            
            return [
                'ID' => $inspection->id,
                'Reference' => $inspection->ref_no,
                'Block' => $inspection->block->name ?? 'N/A',
                'Scheduled Date' => $inspection->scheduled_date_time->format('M d, Y H:i'),
                'Start Date' => $inspection->start_date_time ? $inspection->start_date_time->format('M d, Y H:i') : 'N/A',
                'End Date' => $inspection->end_date_time ? $inspection->end_date_time->format('M d, Y H:i') : 'N/A',
                'Status' => $inspection->status_text,
                'Lead Inspector' => $leadInspector ? $leadInspector->user->name : 'N/A',
                'Team Size' => $inspection->inspectionTeams->count(),
                'Notes' => $inspection->notes ?? 'N/A',
                'Created By' => $inspection->creator->name ?? 'N/A',
                'Created Date' => $inspection->created_at->format('M d, Y'),
            ];
        })->toArray();
    }

    /**
     * Get user types data for export
     */
    private function getUserTypesData(Request $request)
    {
        $query = \App\Models\UserType::withCount('users');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $userTypes = $query->orderBy('created_at', 'desc')->get();

        return $userTypes->map(function ($userType) {
            return [
                'ID' => $userType->id,
                'Name' => $userType->name,
                'Description' => $userType->description ?? 'N/A',
                'Users Count' => $userType->users_count,
                'Created Date' => $userType->created_at->format('M d, Y'),
            ];
        })->toArray();
    }

    /**
     * Get users data for export
     */
    private function getUsersData(Request $request)
    {
        $query = \App\Models\User::with(['userType'])->active();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('userType', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('user_type_id')) {
            $query->where('user_type_id', $request->user_type_id);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return $users->map(function ($user) {
            return [
                'ID' => $user->id,
                'Name' => $user->name,
                'Email' => $user->email,
                'User Type' => $user->userType->name ?? 'N/A',
                'Email Verified' => $user->email_verified_at ? 'Yes' : 'No',
                'Created Date' => $user->created_at->format('M d, Y'),
            ];
        })->toArray();
    }
}
