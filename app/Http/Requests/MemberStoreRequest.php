<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_sei' => 'required|string|max:20',
            'name_mei' => 'required|string|max:20',
            'nickname' => 'required|string|max:10',
            'gender' => 'required|integer|in:1,2', // 1:男性, 2:女性
            'password' => [
                'required',
                'string',
                'min:8',
                'max:20',
                'confirmed', // password_confirmation が必要
                'regex:/^[a-zA-Z0-9]+$/', // rules() で正規表現を配列要素として正しく記述
            ],
            'email' => 'required|string|email|max:200|unique:members,email', // unique:members,email はDBに既に存在するかチェック
        ];
    }

    public function messages(): array
    {
        return [
            // 必須項目 (required)
            'name_sei.required' => '氏名（姓）は必須項目です。',
            'name_mei.required' => '氏名（名）は必須項目です。',
            'nickname.required' => 'ニックネームは必須項目です。',
            'gender.required' => '性別を選択してください。',
            'password.required' => 'パスワードは必須項目です。',
            'email.required' => 'メールアドレスは必須項目です。',
            
            // 文字数制限 (max)
            'name_sei.max' => '氏名（姓）は20文字以内で入力してください。',
            'name_mei.max' => '氏名（名）は20文字以内で入力してください。',
            'nickname.max' => 'ニックネームは10文字以内で入力してください。',
            'password.max' => 'パスワードは20文字以内で入力してください。',
            'email.max' => 'メールアドレスは200文字以内で入力してください。',
            
            // 最小文字数 (min)
            'password.min' => 'パスワードは8文字以上で入力してください。',
            
            // 特定の形式 (email, regex, unique, in, confirmed)
            'email.email' => 'メールアドレスの形式が正しくありません。',
            'email.unique' => 'このメールアドレスは既に登録されています。',
            'gender.in' => '性別の値が不正です。', // 1, 2以外の値が入力された場合
            'password.regex' => 'パスワードは半角英数字で入力してください。',
            'password.confirmed' => 'パスワードが確認用パスワードと一致しません。',
        ];
    }
}
