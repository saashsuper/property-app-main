<!-- Units Header -->
<div class="d-flex align-items-center mb-3 gap-3">
    <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Units</h6>
</div>

@if($block->units && $block->units->count() > 0)
    <div class="table-responsive">
        <table id="blockUnitsTable" class="table table-bordered table-hover w-100">
            <thead class="table-light">
                <tr>
                    <th>Unit Code</th>
                    <th>Unit Name</th>
                    <th>Type</th>
                    <th>Owner's Name</th>
                    <th>Salutation</th>
                    <th>Email</th>
                    <th>Resident</th>
                    <th>Mobile</th>
                    <th>Phone</th>
                    <th>Letting Agent</th>
                    <th>Misc Info</th>
                </tr>
            </thead>
            <tbody>
                @foreach($block->units as $unit)
                    <tr>
                        <td>{{ $unit->unit_code ?? 'N/A' }}</td>
                        <td>{{ $unit->unit_name ?? 'N/A' }}</td>
                        <td>{{ $unit->unitType->name ?? 'N/A' }}</td>
                        <td>{{ $unit->owners_name ?? 'N/A' }}</td>
                        <td>{{ $unit->salutation ?? 'N/A' }}</td>
                        <td>{{ $unit->email ?? 'N/A' }}</td>
                        <td>{{ $unit->resident ? 'Yes' : 'No' }}</td>
                        <td>{{ $unit->mobile_no ?? 'N/A' }}</td>
                        <td>{{ $unit->phone_number ?? 'N/A' }}</td>
                        <td>{{ $unit->letting_agent ?? 'N/A' }}</td>
                        <td>{{ $unit->misc_info ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-4">
        <div class="text-muted">
            <i class="ph-house font-size-24 mb-2"></i>
            <p>No units found for this block.</p>
        </div>
    </div>
@endif

<!-- DataTables CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable for units
    if ($('#blockUnitsTable').length) {
        $('#blockUnitsTable').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
            ],
            autoWidth: false,
            scrollX: true,
            scrollCollapse: true,
            order: [[0, 'asc']], // default sort by Unit Code
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ units per page",
                info: "Showing _START_ to _END_ of _TOTAL_ units",
                infoEmpty: "No units found",
                infoFiltered: "(filtered from _MAX_ total units)",
                zeroRecords: "No units found",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            initComplete: function() {
                // Increase search box size
                $('.dataTables_filter input').addClass('form-control').css({
                    'width': '300px',
                    'height': '38px',
                    'font-size': '14px'
                });
            }
        });
    }
});
</script>
@endpush
