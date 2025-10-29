<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReviewRequest extends FormRequest
{
    /**
     * リクエストがこのアクションを承認されているか確認する。
     * 承認されていない場合、Laravelは自動的に403エラーを返すか、前のページにリダイレクトします。
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // レビュー投稿はログインユーザーのみに許可
        // ReviewControllerでもチェックしているが、FormRequestでもチェックすることでセキュリティを強化
        return Auth::check(); 
    }

    /**
     * バリデーションルールを取得する。
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // rating: 必須、整数、1から5の間
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            // body: 任意、文字列、最大500文字
            'body' => ['required', 'nullable', 'string', 'max:500'],
        ];
    }
    
    /**
     * エラーメッセージの定義
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'rating.required' => '商品評価は必ず選択してください。',
            'rating.integer' => '商品評価は数値で指定してください。',
            'rating.min' => '商品評価は1点以上である必要があります。',
            'rating.max' => '商品評価は5点以下である必要があります。',
            'body.required' => 'コメントは必ず選択してください。',
            'body.string' => 'コメントは文字列で入力してください。',
            'body.max' => 'コメントは500文字以内で入力してください。',
        ];
    }
}
