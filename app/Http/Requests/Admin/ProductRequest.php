<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 管理者向け商品登録・編集共通のForm Request
 * * NOTE: messages() は主に新規登録時のメッセージとして利用されます。
 */
class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // 認証済み管理者なら許可
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // 新規登録・編集共通の基本ルール
        return [
            'name' => ['required', 'string', 'max:100'],
            'member_id' => ['required', 'integer', 'exists:members,id'], 
            'product_category_id' => ['required', 'integer', 'exists:product_categories,id'],
            'product_subcategory_id' => ['required', 'integer', 'exists:product_subcategories,id'],
            // 仕様準拠：必須・500文字以内
            'product_content' => ['required', 'string', 'max:500'],

            // 画像ファイル（送られた場合のみチェック）
            'image_1_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:10240'], 
            'image_2_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:10240'],
            'image_3_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:10240'],
            'image_4_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:10240'],
            
            // 編集時の既存画像保持用（バリデーションは不要）
            'image_1_path' => ['nullable', 'string'],
            'image_2_path' => ['nullable', 'string'],
            'image_3_path' => ['nullable', 'string'],
            'image_4_path' => ['nullable', 'string'],
        ];
    }
    
    /**
     * 属性名（日本語名）
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => '商品名',
            'member_id' => '会員ID',
            'product_category_id' => '大カテゴリ',
            'product_subcategory_id' => '小カテゴリ',
            'product_content' => '商品説明',
            'image_1_file' => '商品写真1',
            'image_2_file' => '商品写真2',
            'image_3_file' => '商品写真3',
            'image_4_file' => '商品写真4',
        ];
    }
    
    /**
     * 新規登録時のカスタムメッセージ
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // 画像関連メッセージ（1〜4を網羅）
            'image_1_file.image' => ':attribute には画像ファイルを選択してください。',
            'image_1_file.mimes' => ':attribute のファイル形式は、JPG、JPEG、PNG、GIFのいずれかにしてください。',
            'image_1_file.max'   => ':attribute のファイルサイズは、10MB以下にしてください。',

            'image_2_file.image' => ':attribute には画像ファイルを選択してください。',
            'image_2_file.mimes' => ':attribute のファイル形式は、JPG、JPEG、PNG、GIFのいずれかにしてください。',
            'image_2_file.max'   => ':attribute のファイルサイズは、10MB以下にしてください。',

            'image_3_file.image' => ':attribute には画像ファイルを選択してください。',
            'image_3_file.mimes' => ':attribute のファイル形式は、JPG、JPEG、PNG、GIFのいずれかにしてください。',
            'image_3_file.max'   => ':attribute のファイルサイズは、10MB以下にしてください。',

            'image_4_file.image' => ':attribute には画像ファイルを選択してください。',
            'image_4_file.mimes' => ':attribute のファイル形式は、JPG、JPEG、PNG、GIFのいずれかにしてください。',
            'image_4_file.max'   => ':attribute のファイルサイズは、10MB以下にしてください。',

            // 汎用メッセージ
            'required' => ':attribute は必須項目です。',
            'integer'  => ':attribute は数値で入力してください。',
            'string'   => ':attribute は文字列で入力してください。',
            'max'      => ':attribute は:max文字以下で入力してください。',
            'min'      => ':attribute は:min以上の値を入力してください。',
            'exists'   => '選択された:attribute は存在しません。',
        ];
    }
}
