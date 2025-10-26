<!-- Block Information Header -->
<div class="d-flex align-items-center mb-3 gap-3">
    <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Block Information</h6>
</div>

@if($blockInformation && $blockInformation->count() > 0)
    <!-- Block Information List -->
    <div class="table-responsive w-100">
        <table id="blockInformationTable" class="table table-bordered table-hover w-100">
            <thead class="table-light">
                <tr>
                    <th>Information Type</th>
                    <th>Description</th>
                    <th>Added Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($blockInformation as $info)
                    <tr>
                        <td>
                            <span class="fw-semibold">{{ $info->informationType->name ?? 'N/A' }}</span>
                        </td>
                        <td>{{ Str::limit($info->description ?? 'No description provided', 50) }}</td>
                        <td>{{ $info->created_at ? $info->created_at->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-4">
        <div class="text-muted">
            <i class="ph-info font-size-24 mb-2"></i>
            <p>No information available for this block.</p>
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
    // Initialize DataTable for block information
    if ($('#blockInformationTable').length) {
        $('#blockInformationTable').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
            ],
            autoWidth: false,
            scrollX: true,
            scrollCollapse: true,
            order: [[2, 'desc']], // default sort by Added Date (newest first)
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ information per page",
                info: "Showing _START_ to _END_ of _TOTAL_ information",
                infoEmpty: "No information found",
                infoFiltered: "(filtered from _MAX_ total information)",
                zeroRecords: "No information found",
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