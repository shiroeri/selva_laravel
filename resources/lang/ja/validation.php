<?php

return [

    /*
    | バリデーションメッセージ
    */

    'required' => ':attribute は必ず入力してください。',
    'email'    => ':attribute は有効なメールアドレス形式で入力してください。',
    'min'      => [
        'string' => ':attribute は :min 文字以上で入力してください。',
    ],
    'max'      => [
        'string' => ':attribute は :max 文字以下で入力してください。',
    ],
    'confirmed' => ':attribute が確認用と一致しません。',
    'string'   => ':attribute は文字列で入力してください。',
    'regex' => ':attribute は正しい形式で入力してください。（半角英数字など）', 

    /*
    | 属性名（フィールド名を日本語に置き換える設定）
    */

    'attributes' => [
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'password_confirmation' => 'パスワード確認',
    ],
];