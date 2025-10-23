<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    // ★論理削除を有効化★
    use HasFactory, SoftDeletes; 

    // ★テーブル名の指定 (デフォルトで product_categories が使用されるため、必須ではないが明示的に指定)★
    protected $table = 'product_categories';

    // ★プライマリキーの型指定 (intに変更したため)★
    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     * 登録・編集を許可するカラム。
     */
    protected $fillable = [
        'name',
    ];

    /**
     * リレーションシップ: このカテゴリに属するサブカテゴリを複数取得
     */
    public function subcategories()
    {
        return $this->hasMany(ProductSubcategory::class);
    }
}
