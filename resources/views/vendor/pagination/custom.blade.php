@if ($paginator->hasPages())
    <style>
        .pagination-input {
            border-radius: 0.375rem 0 0 0.375rem;
            border-right: none;
        }
        .pagination-go-btn {
            border-radius: 0 0.375rem 0.375rem 0;
            border-left: 1px solid #dee2e6;
        }
        .pagination-go-btn:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
        }
        .pagination-input:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .pagination-input:focus + .pagination-go-btn {
            border-color: #86b7fe;
        }
    </style>

    <nav class="d-flex justify-items-center justify-content-between">
        <div class="d-flex justify-content-between flex-fill d-sm-none">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">@lang('pagination.previous')</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">@lang('pagination.previous')</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">@lang('pagination.next')</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">@lang('pagination.next')</span>
                    </li>
                @endif
            </ul>
        </div>

        <div class="d-none d-sm-block w-100">
            <div class="row align-items-center w-100 g-2">
                <div class="col-sm-4 text-start">
                    <p class="small text-muted mb-0">
                        {!! __('Showing') !!}
                        <span class="fw-semibold">{{ $paginator->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="fw-semibold">{{ $paginator->total() }}</span>
                        {!! __('results') !!}
                    </p>
                </div>

                <div class="col-sm-4 d-flex align-items-center justify-content-center">
                    <ul class="pagination mb-0">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                            <span class="page-link" aria-hidden="true">&lsaquo;</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                        </li>
                    @endif

                    {{-- Custom 7-Page Pagination --}}
                    @php
                        $currentPage = $paginator->currentPage();
                        $lastPage = $paginator->lastPage();
                        
                        // Calculate start and end pages for 7-page window
                        $start = max(1, $currentPage - 3);
                        $end = min($lastPage, $currentPage + 3);
                        
                        // Adjust if we're near the beginning or end
                        if ($end - $start < 6) {
                            if ($start == 1) {
                                $end = min($lastPage, $start + 6);
                            } else {
                                $start = max(1, $end - 6);
                            }
                        }
                    @endphp

                    {{-- First Page (if not in range) --}}
                    @if ($start > 1)
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                        </li>
                        @if ($start > 2)
                            <li class="page-item disabled" aria-disabled="true">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                    @endif

                    {{-- Page Numbers --}}
                    @for ($page = $start; $page <= $end; $page++)
                        @if ($page == $currentPage)
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endfor

                    {{-- Last Page (if not in range) --}}
                    @if ($end < $lastPage)
                        @if ($end < $lastPage - 1)
                            <li class="page-item disabled" aria-disabled="true">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->url($lastPage) }}">{{ $lastPage }}</a>
                        </li>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                            <span class="page-link" aria-hidden="true">&rsaquo;</span>
                        </li>
                    @endif
                    </ul>
                </div>

                <div class="col-sm-4 d-flex align-items-center justify-content-end">
                    <span class="text-muted me-2">Go to:</span>
                    <div class="input-group" style="width: 100px;">
                        <input type="number" 
                               class="form-control form-control-sm pagination-input" 
                               min="1" 
                               max="{{ $lastPage }}" 
                               value="{{ $currentPage }}"
                               data-current-page="{{ $currentPage }}"
                               data-last-page="{{ $lastPage }}"
                               data-base-url="{{ $paginator->url(1) }}"
                               style="text-align: center;"
                               placeholder="{{ $currentPage }}">
                        <button class="btn btn-outline-secondary btn-sm pagination-go-btn" type="button" title="Go to page">
                            <i class="ph-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle page number input
        const paginationInputs = document.querySelectorAll('.pagination-input');
        paginationInputs.forEach(function(input) {
            const goBtn = input.nextElementSibling;
            
            // Handle Enter key
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    navigateToPage(input);
                }
            });
            
            // Handle Go button click
            goBtn.addEventListener('click', function() {
                navigateToPage(input);
            });
            
            // Handle input focus
            input.addEventListener('focus', function() {
                this.select();
            });
            
            function navigateToPage(inputElement) {
                const page = parseInt(inputElement.value);
                const currentPage = parseInt(inputElement.dataset.currentPage);
                const lastPage = parseInt(inputElement.dataset.lastPage);
                const baseUrl = inputElement.dataset.baseUrl;
                
                if (page && page >= 1 && page <= lastPage && page !== currentPage) {
                    // Construct the URL for the target page
                    let url = baseUrl;
                    if (page > 1) {
                        // Replace or add page parameter
                        if (url.includes('?')) {
                            if (url.includes('page=')) {
                                url = url.replace(/page=\d+/, 'page=' + page);
                            } else {
                                url += '&page=' + page;
                            }
                        } else {
                            url += '?page=' + page;
                        }
                    }
                    window.location.href = url;
                } else {
                    // Reset to current page if invalid
                    inputElement.value = currentPage;
                    inputElement.focus();
                }
            }
        });
    });
    </script>
@endif
