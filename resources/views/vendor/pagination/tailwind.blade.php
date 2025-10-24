<script src="https://cdn.tailwindcss.com"></script>
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        
        <!-- 狭い画面用の「前へ」「次へ」ボタン (隠れている部分) -->
        <div class="flex flex-1 justify-between sm:hidden">
            {{-- 前へ --}}
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                    {{ __('pagination.previous') }}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    {{ __('pagination.previous') }}
                </a>
            @endif

            {{-- 次へ --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    {{ __('pagination.next') }}
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                    {{ __('pagination.next') }}
                </span>
            @endif
        </div>

        <!-- 広い画面用のページネーション (ページ番号と「前へ」「次へ」) -->
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-center">
            
            {{-- 1. 「前へ」ボタンの表示制御 --}}
            @if (!$paginator->onFirstPage())
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-4 py-2 mr-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="{{ __('pagination.previous') }}">
                    {{ __('pagination.previous') }}
                </a>
            @endif
            
            {{-- 2. ページ番号のリスト --}}
            <span class="relative z-0 inline-flex shadow-sm rounded-md">
                
                {{-- ページ番号の表示ロジックをカスタムし、常に3ページ分を表示 --}}
                @php
                    $currentPage = $paginator->currentPage();
                    $lastPage = $paginator->lastPage();
                    $windowSize = 3;

                    // ページ総数が表示ウィンドウサイズ以下の場合
                    if ($lastPage <= $windowSize) {
                        $startPage = 1;
                        $endPage = $lastPage;
                    } else {
                        // ページ総数がウィンドウサイズより大きい場合 (常に3ページ表示)
                        
                        // 1. デフォルトは現在ページを中心に設定
                        $startPage = $currentPage - 1;
                        $endPage = $currentPage + 1;

                        // 2. 開始位置が1より小さかった場合 (Page 1など)
                        if ($startPage < 1) {
                            $startPage = 1;
                            $endPage = $windowSize; // 1, 2, 3を表示
                        }
                        
                        // 3. 終了位置が最終ページを超えた場合 (最終ページなど)
                        if ($endPage > $lastPage) {
                            $endPage = $lastPage;
                            // 最終ページからウィンドウサイズ分遡る (例: 5, 4, 3)
                            $startPage = $lastPage - ($windowSize - 1); 
                        }
                    }
                @endphp

                {{-- 決定した範囲のページ番号をループして表示 --}}
                @foreach (range($startPage, $endPage) as $page)
                    @php
                        // LaravelのPaginatorから直接URLを取得
                        $url = $paginator->url($page);
                    @endphp

                    @if ($page == $currentPage)
                        <span aria-current="page">
                            <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-white bg-blue-600 border border-blue-600 cursor-default leading-5">{{ $page }}</span>
                        </span>
                    @else
                        <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            </span>

            {{-- 3. 「次へ」ボタンの表示制御 --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="{{ __('pagination.next') }}">
                    {{ __('pagination.next') }}
                </a>
            @endif
        </div>
    </nav>
@endif
