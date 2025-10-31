<!-- Tailwind CSSを想定したシンプルなデザイン -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品レビュー管理</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* カスタムスタイル: 星の表示 */
        .rating-star {
            font-size: 1.5rem; /* 星のサイズを少し大きく */
            line-height: 1;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-4">
    <div class="max-w-4xl mx-auto bg-white p-6 md:p-8 rounded-xl shadow-lg relative">
        
        <!-- タイトル -->
        <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">商品レビュー管理</h1>
        
        <!-- トップに戻るボタン (右上に配置) -->
        <div class="absolute top-6 right-6">
            <a href="{{ route('top') }}" 
               class="px-4 py-2 text-sm font-medium text-white bg-pink-300 rounded-lg hover:bg-pink-400 transition duration-150 shadow-md">
                トップに戻る
            </a>
        </div>

        <!-- ステータスメッセージ表示 -->
        <!-- @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('status') }}</span>
            </div>
        @endif -->
        
        {{-- Strファサードは不要になりましたが、互換性のため残しておきます --}}
        @php use Illuminate\Support\Str; @endphp

        @if ($reviews->isEmpty())
            <p class="text-gray-600">まだレビューが投稿されていません。</p>
        @else
            <!-- レビュー一覧 -->
            <div class="space-y-6">
                @foreach ($reviews as $review)
                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex items-start space-x-4">
                            
                            <!-- 商品画像 -->
                            <div class="flex-shrink-0 w-24 h-24 rounded-md overflow-hidden border border-gray-200">
                                @php
                                    $imagePath = $review->product?->image_1 ?? null; 
                                    $hash = substr(md5($review->product?->name ?? 'default'), 0, 6);
                                @endphp

                                @if ($imagePath)
                                    {{-- 画像パスがあればそれを表示（Storage::urlなどを使う前提） --}}
                                    <img src="{{ asset('storage/' . $imagePath) }}" 
                                         alt="{{ $review->product?->name ?? '商品' }}画像" 
                                         class="w-full h-full object-cover">
                                @else
                                    {{-- 画像パスがなければダミー画像を表示 --}}
                                    <img src="https://placehold.co/96x96/{{ $hash }}/ffffff?text=Product" 
                                         alt="商品画像" 
                                         class="w-full h-full object-cover">
                                @endif
                            </div>

                            <div class="flex-grow">
                                <!-- カテゴリ情報 -->
                                <div class="text-xs font-medium text-gray-500 mb-1">
                                    @php
                                        $mainCategory = $review->product?->subcategory?->category?->name ?? 'カテゴリなし';
                                        $subCategory = $review->product?->subcategory?->name ?? 'サブカテゴリなし';
                                    @endphp
                                    <span class="bg-gray-200 px-2 py-0.5 rounded-full">{{ $mainCategory }} &gt; {{ $subCategory }}</span>
                                </div>

                                <!-- 商品名と評価 -->
                                <h2 class="text-lg font-bold text-gray-800">
                                    {{ $review->product?->name ?? '商品情報なし' }}
                                </h2>
                                
                                <div class="flex items-center my-1">
                                    <!-- 評価 (星) -->
                                    <span class="text-yellow-500 font-bold mr-2">
                                        {{-- ★修正: evaluation カラムを使用★ --}}
                                        @php
                                            $evaluation = $review->evaluation ?? 0;
                                        @endphp
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span class="inline-block rating-star" style="color: {{ $i <= $evaluation ? 'orange' : '#ccc' }};">&#9733;</span>
                                        @endfor
                                    </span>
                                    <span class="text-sm text-gray-600">{{ $evaluation }}</span>
                                    <!-- 投稿日 -->
                                    <!-- <span class="ml-4 text-xs text-gray-400">{{ $review->created_at?->format('Y/m/d') ?? '日付なし' }}</span> -->
                                </div>

                                <!-- レビューコメント -->
                                <p class="text-gray-700 mb-3 text-sm">
                                    {{-- ★修正: mb_substrとmb_strlenを使い、文字数ベースで厳密に15文字に切り詰め、「...」を表示する★ --}}
                                    @php
                                        $comment = $review->comment ?? 'コメントなし';
                                        $limit = 15;
                                        // mb_strlenで文字数をカウントし、超過していればmb_substrで切り詰める
                                        $truncatedComment = (mb_strlen($comment, 'UTF-8') > $limit)
                                            ? mb_substr($comment, 0, $limit, 'UTF-8') . '...'
                                            : $comment;
                                    @endphp
                                    {{ $truncatedComment }}
                                </p>

                                <!-- アクションボタン -->
                                <div class="flex space-x-3">
                                    <!-- 編集ボタン -->
                                    <a href="{{ route('mypage.reviews.edit', $review) }}" 
                                       class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition duration-150 shadow">
                                        レビュー編集
                                    </a>
                                    <!-- 削除確認ボタン -->
                                    <a href="{{ route('mypage.reviews.deleteConfirm', $review) }}" 
                                       class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 transition duration-150 shadow">
                                        レビュー削除
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- ページネーションリンク -->
            <div class="mt-8">
                {{ $reviews->links() }}
            </div>
        @endif
        
        <!-- マイページに戻るボタン -->
        <div class="mt-8 pt-6 border-t border-gray-200 text-center">
            <a href="{{ route('mypage.index') }}" 
               class="inline-flex items-center px-6 py-3 text-base font-medium rounded-lg text-blue-600 bg-white border-2 border-blue-600 hover:bg-blue-50 transition duration-150 shadow-md">
                マイページに戻る
            </a>
        </div>

    </div>
</body>
</html>
