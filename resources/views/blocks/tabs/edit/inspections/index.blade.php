<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center mb-3 gap-3">
            <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Inspection History</h6>
            <div class="d-flex align-items-center gap-2">
                <!-- Export Buttons -->
                <div class="btn-group" role="group">
                    <a href="{{ route('export.pdf', 'block-inspections') }}?block_id={{ $block->id }}" 
                       id="exportPdfBtn"
                       class="btn btn-outline-danger btn-sm {{ (!$blockInspections || $blockInspections->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$blockInspections || $blockInspections->count() === 0) ? 'No data to export' : 'Export to PDF' }}"
                       {{ (!$blockInspections || $blockInspections->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-file-pdf"></i>
                    </a>
                    <a href="{{ route('export.excel', 'block-inspections') }}?block_id={{ $block->id }}" 
                       id="exportExcelBtn"
                       class="btn btn-outline-success btn-sm {{ (!$blockInspections || $blockInspections->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$blockInspections || $blockInspections->count() === 0) ? 'No data to export' : 'Export to Excel' }}"
                       {{ (!$blockInspections || $blockInspections->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-file-xls"></i>
                    </a>
                    <a href="{{ route('export.print', 'block-inspections') }}?block_id={{ $block->id }}" 
                       id="exportPrintBtn"
                       class="btn btn-outline-secondary btn-sm {{ (!$blockInspections || $blockInspections->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$blockInspections || $blockInspections->count() === 0) ? 'No data to export' : 'Print' }}" 
                       target="_blank"
                       {{ (!$blockInspections || $blockInspections->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-printer"></i>
                    </a>
                </div>
                <button class="btn btn-primary custom-toggle active" data-bs-toggle="modal" data-bs-target="#addInspectionModal">
                    <i class="ph-plus align-bottom me-1"></i> Add Inspection
                </button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table id="inspectionsTable" class="table table-bordered table-hover w-100">
                        <thead class="table-light">
                            <tr>
                                <th class="min-width-sm">Reference</th>
                                <th class="min-width-sm">Inspection Date</th>
                                <th class="min-width-md">Inspector</th>
                                <th class="min-width-sm">Status</th>
                                <th class="min-width-lg">Notes</th>
                                <th class="text-center min-width-sm">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($blockInspections && $blockInspections->count() > 0)
                                @foreach($blockInspections as $inspection)
                                    @php
                                        $leadInspector = $inspection->inspectionTeams->where('is_lead', true)->first();
                                        $displayInspector = $leadInspector ? ($leadInspector->user->name ?? 'N/A') : ($inspection->creator->name ?? 'N/A');
                                        $userForEdit = $leadInspector ? $leadInspector->user_id : $inspection->created_by;
                                    @endphp
                                    <tr>
                                        <td class="align-middle">{{ $inspection->ref_no ?? 'N/A' }}</td>
                                        <td class="align-middle text-nowrap">
                                            {{ $inspection->scheduled_date_time ? \Carbon\Carbon::parse($inspection->scheduled_date_time)->format('M d, Y H:i') : 'N/A' }}
                                        </td>
                                        <td class="align-middle">{{ $displayInspector }}</td>
                                        <td class="align-middle text-nowrap">
                                            <span class="badge bg-{{ $inspection->status_color }}-subtle text-{{ $inspection->status_color }}">
                                                {{ $inspection->status_text }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-wrap">{{ Str::limit($inspection->notes, 80) ?? 'N/A' }}</td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-sm btn-outline-primary edit-inspection"
                                                        data-inspection-id="{{ $inspection->id }}"
                                                        data-user-id="{{ $userForEdit ?? '' }}"
                                                        data-date="{{ $inspection->scheduled_date_time ? \Carbon\Carbon::parse($inspection->scheduled_date_time)->format('Y-m-d') : '' }}"
                                                        data-time="{{ $inspection->scheduled_date_time ? \Carbon\Carbon::parse($inspection->scheduled_date_time)->format('H:i') : '' }}"
                                                        data-notes="{{ e($inspection->notes) }}"
                                                        title="Edit Inspection">
                                                    <i class="ph-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning" 
                                                        onclick="window.location.href='{{ route('block-inspections.edit', $inspection->id) }}'"
                                                        title="Update Inspection Details">
                                                    <i class="ph-note-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger"
                                                        onclick="inspectionShowDeleteConfirmation({{ $inspection->id }}, {
                                                            ref_no: '{{ $inspection->ref_no ?? 'N/A' }}',
                                                            scheduled_date: '{{ $inspection->scheduled_date_time ? \Carbon\Carbon::parse($inspection->scheduled_date_time)->format('M d, Y H:i') : 'N/A' }}',
                                                            inspector: '{{ $displayInspector }}'
                                                        })"
                                                        title="Delete Inspection">
                                                    <i class="ph-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
    </div>
</div>

@include('blocks.tabs.edit.inspections.modals')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css" />

<style>
    #inspectionsTable thead th {
        white-space: nowrap;
        font-size: 0.85rem;
    }

    #inspectionsTable tbody td {
        vertical-align: middle;
        font-size: 0.85rem;
    }

    #inspectionsTable .text-wrap {
        white-space: normal;
        word-wrap: break-word;
    }

    #inspectionsTable .min-width-sm { min-width: 120px; }
    #inspectionsTable .min-width-md { min-width: 160px; }
    #inspectionsTable .min-width-lg { min-width: 220px; }

    .badge-inspection-created { background-color: #6c757d; color: white; }
    .badge-inspection-progress { background-color: #0d6efd; color: white; }
    .badge-inspection-workorder { background-color: #fd7e14; color: white; }
    .badge-inspection-completed { background-color: #198754; color: white; }
    .badge-inspection-invoiced { background-color: #6f42c1; color: white; }
    .badge-inspection-default { background-color: #6c757d; color: white; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

<script>
    window.inspectionShowDeleteConfirmation = window.inspectionShowDeleteConfirmation || function() {};
    window.inspectionAttachEditHandlers = window.inspectionAttachEditHandlers || function() {};
</script>

@include('blocks.tabs.edit.inspections.scripts')
@endpush

