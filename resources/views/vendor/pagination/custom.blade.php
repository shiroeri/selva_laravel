@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center items-center space-x-1">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 text-gray-400 border rounded-lg cursor-not-allowed">
                前へ
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-3 py-1 text-blue-500 border rounded-lg hover:bg-blue-50 transition duration-150">
                前へ
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="px-3 py-1 text-gray-500">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        {{-- Current Page --}}
                        <span class="px-3 py-1 border rounded-lg pagination-active">
                            {{ $page }}
                        </span>
                    @else
                        {{-- Regular Page Link: 
                           検索/並べ替えパラメータを次のページリンクにも引き継ぐために withQueryString() を使用したURLを使います。
                        --}}
                        <a href="{{ $url }}" class="px-3 py-1 text-gray-700 border rounded-lg hover:bg-gray-100 transition duration-150">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-3 py-1 text-blue-500 border rounded-lg hover:bg-blue-50 transition duration-150">
                次へ
            </a>
        @else
            <span class="px-3 py-1 text-gray-400 border rounded-lg cursor-not-allowed">
                次へ
            </span>
        @endif
    </nav>
@endif
