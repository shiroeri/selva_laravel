<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberChangeRequest extends FormRequest
{
    /**
     * ユーザーがこのリクエストを行う権限があるか判定します。
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // 認証されたユーザーのみがフォームを送信できるようにする場合は true に設定
        return true; 
    }

    /**
     * リクエストに適用されるバリデーションルールを取得します。
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // 適切なバリデーションルールを定義してください
        return [
            'name_sei' => ['required', 'string', 'max:50'],
            'name_mei' => ['required', 'string', 'max:50'],
            'nickname' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:1,2'],
        ];
    }

    /**
     * バリデーションエラーメッセージ内のプレースホルダーを置き換える属性名を取得します。
     * * 例: ":attribute は必須です" の ":attribute" がここで定義した日本語名に置き換わります。
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name_sei' => '姓',
            'name_mei' => '名',
            'nickname' => 'ニックネーム',
            'gender' => '性別',
        ];
    }

    /**
     * カスタムエラーメッセージを定義する場合に使用します。
     * 必要に応じてコメントを外して使用してください。
     *
     * @return array
     */
    
    public function messages(): array
    {
        return [
            'name_sei.required' => '氏名の姓は必ず入力してください。',
            'nickname.max' => 'ニックネームは :max 文字以内で入力してください。',
        ];
    }
    
}
