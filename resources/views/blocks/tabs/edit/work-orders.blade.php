<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center mb-3 gap-3">
            <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Work Orders</h6>
            <button class="btn btn-primary custom-toggle active">
                <i class="ph-plus align-bottom me-1"></i> Create Work Order
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="workOrdersTable">
                <thead class="table-light">
                    <tr>
                        <th>Work Order #</th>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($blockWorkOrders) && $blockWorkOrders->count() > 0)
                        @foreach($blockWorkOrders as $workOrder)
                            <tr>
                                <td>#{{ $workOrder->id }}</td>
                                <td>{{ $workOrder->issue ?? 'N/A' }}</td>
                                <td>
                                    @if($workOrder->priority_id == 1)
                                        <span class="badge bg-success">Low</span>
                                    @elseif($workOrder->priority_id == 2)
                                        <span class="badge bg-info">Normal</span>
                                    @elseif($workOrder->priority_id == 3)
                                        <span class="badge bg-warning">High</span>
                                    @elseif($workOrder->priority_id == 4)
                                        <span class="badge bg-danger">Urgent</span>
                                    @elseif($workOrder->priority_id == 5)
                                        <span class="badge bg-dark">Critical</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    @if($workOrder->status == 1)
                                        <span class="badge bg-warning">Open</span>
                                    @elseif($workOrder->status == 2)
                                        <span class="badge bg-info">In Progress</span>
                                    @else
                                        <span class="badge bg-success">Completed</span>
                                    @endif
                                </td>
                                <td>{{ $workOrder->created_at ? $workOrder->created_at->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">View</button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- DataTables CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#workOrdersTable').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
        ],
        autoWidth: false,
        scrollX: true,
        scrollCollapse: true,
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "",
            infoFiltered: "(filtered from _MAX_ total entries)",
            zeroRecords: "No Work Order Informations found",
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
});
</script>
