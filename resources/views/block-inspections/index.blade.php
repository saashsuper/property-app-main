@extends('layouts.master')

@section('title') @lang('translation.block-inspections') @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ URL::asset('build/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') @lang('translation.blocks') @endslot
        @slot('title') @lang('translation.block-inspections') @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Block Inspections List</h4>
                        <div class="d-flex align-items-center gap-3">
                            <!-- Export Buttons -->
                            <div class="btn-group" role="group">
                                <a href="{{ route('export.pdf', 'block-inspections') }}?{{ http_build_query(request()->query()) }}" class="btn btn-outline-danger btn-sm" title="Export to PDF"><i class="ph-file-pdf"></i></a>
                                <a href="{{ route('export.excel', 'block-inspections') }}?{{ http_build_query(request()->query()) }}" class="btn btn-outline-success btn-sm" title="Export to Excel"><i class="ph-file-xls"></i></a>
                                <a href="{{ route('export.print', 'block-inspections') }}?{{ http_build_query(request()->query()) }}" class="btn btn-outline-secondary btn-sm" title="Print" target="_blank"><i class="ph-printer"></i></a>
                            </div>
                            <!-- Search Form -->
                            <form action="{{ route('block-inspections.index') }}" method="GET" class="d-flex">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" placeholder="Search inspections..." value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="ph-magnifying-glass"></i>
                                    </button>
                                </div>
                            </form>
                            <!-- Add Button -->
                            @admin
                            <a href="{{ route('block-inspections.create') }}" class="btn btn-primary">
                                <i class="ph-plus me-2"></i>Schedule Inspection
                            </a>
                            @endadmin
                        </div>
                    </div>

                    <!-- Filters -->
                    <form action="{{ route('block-inspections.index') }}" method="GET">
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="inspection-status" name="status" onchange="this.form.submit()">
                                    <option value="">All Statuses</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>In Progress</option>
                                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Completed</option>
                                    <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="5" {{ request('status') == '5' ? 'selected' : '' }}>On Hold</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">Date From</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}" onchange="this.form.submit()">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">Date To</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}" onchange="this.form.submit()">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <a href="{{ route('block-inspections.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mt-4">
                        <table class="table table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Block</th>
                                    <th>Scheduled Date</th>
                                    <th>Status</th>
                                    <th>Lead Inspector</th>
                                    <th>Team Size</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inspections as $inspection)
                                    <tr>
                                        <td>
                                            <a href="{{ route('block-inspections.show', $inspection->id) }}" class="text-primary fw-bold">
                                                {{ $inspection->ref_no }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('blocks.show', $inspection->block->id) }}" class="text-decoration-none">
                                                {{ $inspection->block->name }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $inspection->scheduled_date_time->format('M d, Y H:i') }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $inspection->status_color }}-subtle text-{{ $inspection->status_color }}">
                                                {{ $inspection->status_text }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $leadInspector = $inspection->inspectionTeams->where('is_lead', true)->first();
                                            @endphp
                                            {{ $leadInspector ? $leadInspector->user->name : 'N/A' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info">
                                                {{ $inspection->inspectionTeams->count() }} members
                                            </span>
                                        </td>
                                        <td>
                                            {{ $inspection->creator->name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('block-inspections.show', $inspection->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="ph-eye"></i>
                                                </a>
                                                @admin
                                                <a href="{{ route('block-inspections.edit', $inspection->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="ph-pencil"></i>
                                                </a>
                                                <form action="{{ route('block-inspections.destroy', $inspection->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this inspection?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="ph-trash"></i>
                                                    </button>
                                                </form>
                                                @endadmin
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ph-clipboard-text display-4"></i>
                                                <h5 class="mt-2">No inspections found</h5>
                                                <p>No block inspections match your current filters.</p>
                                                @admin
                                                <a href="{{ route('block-inspections.create') }}" class="btn btn-primary">
                                                    <i class="ph-plus me-2"></i>Schedule First Inspection
                                                </a>
                                                @endadmin
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $inspections->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Required datatable js -->
    <script src="{{ URL::asset('build/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Buttons examples -->
    <script src="{{ URL::asset('build/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
    <!-- Responsive examples -->
    <script src="{{ URL::asset('build/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('.table').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });
    </script>
@endsection
