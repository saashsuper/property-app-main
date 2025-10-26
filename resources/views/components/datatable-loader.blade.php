{{-- DataTable Loading Spinner Component --}}
@props([
    'id' => 'table-loading',
    'message' => 'Loading data...',
    'tableId' => 'dataTable'
])

<!-- Loading Spinner -->
<div id="{{ $id }}" class="datatable-loading-spinner text-center py-5">
    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p class="mt-3 text-muted">{{ $message }}</p>
</div>

@once
@push('styles')
<style>
/* DataTable Loading Spinner - Reusable Component */
.datatable-loading-spinner {
    display: block;
}

.datatable-loading-spinner.d-none {
    display: none !important;
}

/* Hide table until DataTables is initialized */
.dt-loading-table {
    display: none;
}

.dt-loading-table.dt-initialized {
    display: table;
}
</style>
@endpush

@push('scripts')
<script>
// Helper function to initialize DataTable with loading spinner
window.initDataTableWithLoader = function(tableSelector, loaderId, options = {}) {
    const $table = $(tableSelector);
    const $loader = $('#' + loaderId);
    
    // Add loading class to table
    $table.addClass('dt-loading-table');
    
    // Merge default options with provided options
    const defaultOptions = {
        initComplete: function() {
            // Hide loader and show table
            $loader.addClass('hidden');
            $table.addClass('dt-initialized');
            
            // Call user's initComplete if provided
            if (options.userInitComplete) {
                options.userInitComplete.call(this);
            }
        }
    };
    
    // Merge options
    const finalOptions = $.extend(true, {}, options, defaultOptions);
    
    // Remove userInitComplete from final options as it's already handled
    delete finalOptions.userInitComplete;
    
    // Initialize DataTable
    return $table.DataTable(finalOptions);
};
</script>
@endpush
@endonce

