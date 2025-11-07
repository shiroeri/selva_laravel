<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * ユーザーがこのリクエストを行う権限があるか
     *
     * @return bool
     */
    public function authorize()
    {
        // 管理者権限が必要なため、適切に設定
        return true; 
    }

    /**
     * バリデーションルールを取得
     *
     * @return array
     */
    public function rules()
    {
        // 登録・編集の共通ルール
        $rules = [
            'category_name' => ['required', 'string', 'max:20'],
        ];

        // 最初のサブカテゴリフィールド (subcategories.0) は必須とする
        $rules["subcategories.0"] = ['required', 'string', 'max:20'];

        // 残りの9個のサブカテゴリフィールドに対してルールを適用
        for ($i = 1; $i < 10; $i++) {
            $rules["subcategories.{$i}"] = ['nullable', 'string', 'max:20'];
        }

        // 必須のサブカテゴリが1つ以上あるかチェック (設計書: 「1つ以上入力」が必要)
        // subcategories.0 を required に設定することで、この要件を満たす
        return $rules;
    }
    
    /**
     * 属性名の定義
     *
     * @return array
     */
    public function attributes()
    {
        $attributes = [
            'category_name' => '商品大カテゴリ',
        ];

        for ($i = 0; $i < 10; $i++) {
            // 1から始まる番号で表示
            $attributes["subcategories.{$i}"] = '商品小カテゴリ'; 
        }

        return $attributes;
    }
}