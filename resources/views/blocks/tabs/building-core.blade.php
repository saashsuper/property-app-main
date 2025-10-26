        <div class="d-flex align-items-center mb-3 gap-3">
            <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Building Information</h6>
        </div>
        
@if($block->buildings && $block->buildings->count() > 0)
        <div class="table-responsive w-100">
            <table class="table table-bordered table-hover w-100" id="building-info-table">
                <thead class="table-light">
                    <tr>
                        <th>Building Type</th>
                        <th>Building Name</th>
                        <th>No of Floors</th>
                        <th>Roof Type</th>
                        <th>No of Lifts</th>
                        <th>Created Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($block->buildings as $building)
                        <tr>
                            <td>{{ $building->buildingType->name ?? 'N/A' }}</td>
                            <td>{{ $building->name ?? 'N/A' }}</td>
                            <td>{{ $building->floor_no ?? 'N/A' }}</td>
                            <td>{{ $building->roof_type ?? 'N/A' }}</td>
                            <td>{{ $building->no_lift ?? 'N/A' }}</td>
                            <td>{{ $building->created_at ? $building->created_at->format('M d, Y') : 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
@else
        <div class="text-center py-4">
            <div class="text-muted">
                <i class="ph-buildings font-size-24 mb-2"></i>
                <p>No buildings found for this block.</p>
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
    // Initialize DataTable for buildings
    if ($('#building-info-table').length) {
        $('#building-info-table').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
            ],
            autoWidth: false,
            scrollX: true,
            scrollCollapse: true,
            order: [[5, 'desc']], // default sort by Created Date (newest first)
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ buildings per page",
                info: "Showing _START_ to _END_ of _TOTAL_ buildings",
                infoEmpty: "No buildings found",
                infoFiltered: "(filtered from _MAX_ total buildings)",
                zeroRecords: "No buildings found",
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