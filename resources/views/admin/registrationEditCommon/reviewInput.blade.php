<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family:'Inter','Noto Sans JP',sans-serif; background:#f3f4f6; }
    </style>
</head>
<body class="min-h-screen flex items-start justify-center pt-10 pb-14">
<div class="w-full max-w-3xl bg-white rounded-xl shadow-2xl overflow-hidden">

    <div class="bg-gray-100 p-6 flex items-center justify-between border-b">
        <h1 class="text-2xl font-bold text-gray-800">{{ $pageTitle }}</h1>
        <a href="{{ route('admin.review.index') }}"
           class="px-4 py-2 text-sm bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">一覧に戻る</a>
    </div>

    <div class="p-6">
        <form action="{{ $isEdit ? route('admin.review.updateConfirm', $review->id) : route('admin.review.confirm') }}"
              method="POST" class="space-y-6">
            @csrf
            @if($isEdit) @method('PUT') @endif

            {{-- 商品 --}}
            <div>
                <label for="product_id" class="block text-sm font-medium text-gray-700">商品</label>
                <select id="product_id" name="product_id"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">選択してください</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ old('product_id', $input['product_id'] ?? '') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
                @error('product_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- 会員 --}}
            <div>
                <label for="member_id" class="block text-sm font-medium text-gray-700">会員</label>
                <select id="member_id" name="member_id"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">選択してください</option>
                    @foreach($members as $m)
                        <option value="{{ $m->id }}" {{ old('member_id', $input['member_id'] ?? '') == $m->id ? 'selected' : '' }}>
                            {{ $m->name_sei }} {{ $m->name_mei }}
                        </option>
                    @endforeach
                </select>
                @error('member_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- ID（編集時 or 登録時） --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">ID</label>
                @if($isEdit)
                    {{-- 編集時：登録済みのIDを表示 --}}
                    <div class="mt-1 px-3 py-2 bg-gray-50 border rounded-md">{{ $review->id }}</div>
                @else
                    {{-- 登録時：自動採番メッセージ --}}
                    <div class="mt-1 px-3 py-2 bg-gray-50 border rounded-md text-gray-500">
                        登録後に自動採番
                    </div>
                @endif
            </div>

            {{-- 商品評価 --}}
            <div>
                <label for="evaluation" class="block text-sm font-medium text-gray-700">商品評価</label>
                <select id="evaluation" name="evaluation"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">選択してください</option>
                    @for($i=1;$i<=5;$i++)
                        <option value="{{ $i }}" {{ old('evaluation', $input['evaluation'] ?? '') == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
                @error('evaluation') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- 商品コメント --}}
            <div>
                <label for="comment" class="block text-sm font-medium text-gray-700">商品コメント</label>
                <textarea id="comment" name="comment" rows="5"
                          class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                          >{{ old('comment', $input['comment'] ?? '') }}</textarea>
                @error('comment') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">
                    確認画面へ
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
