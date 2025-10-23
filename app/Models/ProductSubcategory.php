<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSubcategory extends Model
{
    // ★論理削除を有効化★
    use HasFactory, SoftDeletes; 

    // ★テーブル名の指定 (デフォルトで product_subcategories が使用されるため、必須ではないが明示的に指定)★
    protected $table = 'product_subcategories';

    // ★プライマリキーの型指定 (intに変更したため)★
    protected $keyType = 'int';

    // ★登録・編集を許可するカラム★
    protected $fillable = [
        'product_category_id',
        'name',
    ];

    /**
     * リレーションシップ: 所属する親カテゴリを一つ取得
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
}
