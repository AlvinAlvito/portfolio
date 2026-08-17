@if ($paginator->hasPages())
    <nav class="avinto-pagination" role="navigation" aria-label="Navigasi halaman proyek">
        <p>Menampilkan <strong>{{ $paginator->firstItem() }}-{{ $paginator->lastItem() }}</strong> dari <strong>{{ $paginator->total() }}</strong> proyek</p>
        <div class="avinto-pagination-links">
            @if ($paginator->onFirstPage())
                <span class="pagination-arrow disabled" aria-disabled="true"><i data-lucide="arrow-left"></i><span>Sebelumnya</span></span>
            @else
                <a class="pagination-arrow" href="{{ $paginator->previousPageUrl() }}" rel="prev"><i data-lucide="arrow-left"></i><span>Sebelumnya</span></a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pagination-dots">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pagination-page active" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="pagination-page" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="pagination-arrow" href="{{ $paginator->nextPageUrl() }}" rel="next"><span>Berikutnya</span><i data-lucide="arrow-right"></i></a>
            @else
                <span class="pagination-arrow disabled" aria-disabled="true"><span>Berikutnya</span><i data-lucide="arrow-right"></i></span>
            @endif
        </div>
    </nav>
@endif
