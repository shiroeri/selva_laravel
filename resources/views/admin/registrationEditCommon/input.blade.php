<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? '会員フォーム' }}</title>
    <!-- Tailwind CSS CDN (見た目調整のため) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    </style>
</head>
<body class="bg-blue-50 min-h-screen flex items-start justify-center pt-12 pb-12">

    @php
        // フォームが利用するDBの初期値データを取得
        $initialMemberData = $member ?? (object)[
            'id' => null, 
            'name_sei' => null, 
            'name_mei' => null, 
            'nickname' => null, 
            'gender' => null, 
            'email' => null
        ];
        
        // ★修正点1: コントローラーから渡されたセッションデータ（$data）をフォーム表示用データにマージする
        // $dataは連想配列（array）で渡ってくる想定
        $formData = (array)$initialMemberData; // DBデータを配列に変換
        
        // $dataが存在し、かつ配列であればマージする
        if (isset($data) && is_array($data)) {
            // セッションデータ（$data）をDBデータ（$formData）に上書きする（確認画面からの戻りを優先）
            // パスワード関連は$initialMemberDataに入っていないので、ここでは明示的にマージする
            $formData = array_merge($formData, $data);
        }

        // Blade内でオブジェクトとしてアクセスしやすいように再度オブジェクト化（オプション）
        $formDataObject = (object)$formData;
    @endphp

    <div class="space-y-6 max-w-4xl mx-auto p-0 bg-white shadow-2xl rounded-xl w-full overflow-hidden">
        
        <!-- 1. ページヘッダー (グレー背景) -->
        <div class="bg-gray-100 p-6 flex items-center justify-between border-b border-gray-200">
            <h2 class="text-3xl font-extrabold text-gray-800">{{ $pageTitle ?? 'フォーム' }}</h2>
            <!-- 戻るボタン（戻るリンクが設定されている場合） -->
            @if(isset($backLink))
                <a href="{{ $backLink }}" class="px-5 py-2 text-sm font-medium bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 shadow-md">
                    一覧へ戻る
                </a>
            @endif
        </div>


        <!-- 2. フォーム本体 -->
        <div class="p-8">
            <!-- フォームアクションの設定 -->
            {{-- 編集時はIDを使用して更新ルートへ、登録時は確認ルートへ --}}
            <form action="{{ $isEdit ? route($routePrefix . '.updateConfirm', $initialMemberData->id) : route($routePrefix . '.confirm') }}" method="POST">
                @csrf
                @if($isEdit)
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $initialMemberData->id }}">
                @endif

                <div class="space-y-6">

                    <!-- ID -->
                    <div class="flex items-center border-b pb-4">
                        <label class="w-1/4 text-base font-medium text-gray-700">ID</label>
                        <div class="w-3/4 text-base text-gray-900 font-mono">
                            @if($isEdit)
                                {{ $initialMemberData->id }}
                            @else
                                登録後に自動採番
                            @endif
                        </div>
                    </div>

                    <!-- 氏名 (姓・名) - 横並び -->
                    <div class="flex items-start pt-4">
                        <label class="w-1/4 text-base font-medium text-gray-700 pt-2">氏名</label>
                        <div class="w-3/4 flex space-x-6">
                            <!-- 姓 -->
                            <div class="flex-1">
                                <label for="name_sei" class="block text-xs font-normal text-gray-500 mb-1">姓</label>
                                {{-- ★修正点2: old()の後に $formDataObject->name_sei を設定する --}}
                                <input type="text" name="name_sei" id="name_sei"
                                       value="{{ old('name_sei', $formDataObject->name_sei ?? '') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500">
                                @error('name_sei')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <!-- 名 -->
                            <div class="flex-1">
                                <label for="name_mei" class="block text-xs font-normal text-gray-500 mb-1">名</label>
                                {{-- ★修正点2: old()の後に $formDataObject->name_mei を設定する --}}
                                <input type="text" name="name_mei" id="name_mei"
                                       value="{{ old('name_mei', $formDataObject->name_mei ?? '') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500">
                                @error('name_mei')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- ニックネーム -->
                    <div class="flex items-center">
                        <label for="nickname" class="w-1/4 text-base font-medium text-gray-700">ニックネーム</label>
                        <div class="w-3/4">
                            {{-- ★修正点2: old()の後に $formDataObject->nickname を設定する --}}
                            <input type="text" name="nickname" id="nickname"
                                   value="{{ old('nickname', $formDataObject->nickname ?? '') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500">
                            @error('nickname')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <!-- 性別 - ラジオボタン -->
                    <div class="flex items-center">
                        <label class="w-1/4 text-base font-medium text-gray-700">性別</label>
                        <div class="w-3/4 flex space-x-8">
                            @php
                                // ★修正点3: old()の値、セッションの値 ($formDataObject->gender)、DBの値の優先順位で取得
                                $genderValue = old('gender', $formDataObject->gender ?? 0);
                            @endphp
                            <!-- 男性 (gender: 1) -->
                            <div class="flex items-center">
                                <input id="gender_male" name="gender" type="radio" value="1"
                                       class="focus:ring-blue-500 h-5 w-5 text-blue-600 border-gray-300"
                                       @if($genderValue == 1) checked @endif>
                                <label for="gender_male" class="ml-2 block text-base text-gray-900">男性</label>
                            </div>
                            <!-- 女性 (gender: 2) -->
                            <div class="flex items-center">
                                <input id="gender_female" name="gender" type="radio" value="2"
                                       class="focus:ring-blue-500 h-5 w-5 text-blue-600 border-gray-300"
                                       @if($genderValue == 2) checked @endif>
                                <label for="gender_female" class="ml-2 block text-base text-gray-900">女性</label>
                            </div>
                            @error('gender')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <!-- パスワード -->
                    <div class="flex items-center">
                        {{-- 登録時のみ必須 --}}
                        <label for="password" class="w-1/4 text-base font-medium text-gray-700 @if(!$isEdit) @endif">
                            パスワード
                        </label>
                        <div class="w-3/4">
                            {{-- ★修正点4: パスワードフィールドは値をセットしない（セキュリティ上の理由）。old()も使わない。 --}}
                            <input type="password" name="password" id="password"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500">
                            @if($isEdit)
                                <p class="text-xs text-gray-500 mt-1">※変更する場合のみ入力してください（半角英数8〜20文字）。</p>
                            @else
                                <p class="text-xs text-gray-500 mt-1">半角英数8〜20文字で入力してください。</p>
                            @endif
                            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <!-- パスワード確認 -->
                    <div class="flex items-center">
                        {{-- 登録時のみ必須 --}}
                        <label for="password_confirmation" class="w-1/4 text-base font-medium text-gray-700 @if(!$isEdit) @endif">
                            パスワード確認
                        </label>
                        <div class="w-3/4">
                            {{-- ★修正点4: パスワード確認フィールドは値をセットしない（セキュリティ上の理由）。old()も使わない。 --}}
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500">
                            @error('password_confirmation')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <!-- メールアドレス -->
                    <div class="flex items-center">
                        <label for="email" class="w-1/4 text-base font-medium text-gray-700">メールアドレス</label>
                        <div class="w-3/4">
                            {{-- ★修正点2: old()の後に $formDataObject->email を設定する --}}
                            <input type="text" name="email" id="email"
                                   value="{{ old('email', $formDataObject->email ?? '') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500">
                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                </div>

                <div class="mt-10 flex justify-center space-x-4">
                    <button type="submit"
                            class="px-10 py-3 bg-blue-600 text-white font-bold text-lg rounded-xl shadow-xl hover:bg-blue-700 transition duration-150 transform hover:scale-105">
                        確認画面へ
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
