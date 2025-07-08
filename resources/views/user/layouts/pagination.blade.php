@if($paginator->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Hiển thị {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }}
            của {{ $paginator->total() }} {{ $itemName ?? 'kết quả' }}
        </div>

        <nav aria-label="Pagination">
            <ul class="pagination">
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->appends(request()->query())->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a>
                    </li>
                @endif

                @php
                    $start = max($paginator->currentPage() - 2, 1);
                    $end = min($start + 4, $paginator->lastPage());
                    $start = max($end - 4, 1);
                @endphp

                @if($start > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->appends(request()->query())->url(1) }}">1</a>
                    </li>
                    @if($start > 2)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $paginator->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $i }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->appends(request()->query())->url($i) }}">{{ $i }}</a>
                        </li>
                    @endif
                @endfor

                @if($end < $paginator->lastPage())
                    @if($end < $paginator->lastPage() - 1)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->appends(request()->query())->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
                    </li>
                @endif

                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->appends(request()->query())->nextPageUrl() }}"><i class="bi bi-chevron-right"></i></a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link"><i class="bi bi-chevron-right"></i></span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif
