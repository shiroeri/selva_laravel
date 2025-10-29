<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// 必要なリレーション先モデルをインポート
use App\Models\ProductSubcategory; 
use App\Models\ProductCategory; // ★追加：ProductCategoryモデルをインポート★
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    // 論理削除機能を使用
    use HasFactory, SoftDeletes; 

    // ★テーブル名の指定 (デフォルトで products が使用されるため、必須ではないが明示的に指定)★
    protected $table = 'products';

    // ★プライマリキーの型指定 (intに変更したため)★
    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     * 一括代入（Product::create()）を許可するカラムを定義します。
     * ProductControllerのexecuteStoreメソッドで使用するカラムをすべて含めます。
     */
    protected $fillable = [
        'member_id',
        'product_category_id',
        'product_subcategory_id',
        'name',
        'product_content',
        'image_1', // 画像パスを保存する場合
        'image_2', 
        'image_3',
        'image_4',
        // 'price', 'state' など、productsテーブルの他のカラムも必要に応じて追加
    ];

    /**
     * リレーションシップ: この商品を出品した会員
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
    
    /**
     * Product は複数のレビューを持つ (1対多)
     *
     * @return HasMany
     */
    public function reviews(): HasMany
    {
        // 関連する Review モデルを指定します。
        // デフォルトでは、'reviews' テーブルと 'product_id' 外部キーが使用されます。
        return $this->hasMany(Review::class);
    }

    /**
     * リレーションシップ: この商品が属するサブカテゴリ
     */
    public function subcategory()
    {
        // 外部キー 'product_subcategory_id' を使用して ProductSubcategory モデルと関連付けます。
        return $this->belongsTo(ProductSubcategory::class, 'product_subcategory_id');
    }

    /**
     * リレーションシップ: この商品が属するカテゴリ (大)
     * ★追加されたメソッド★
     */
    public function category()
    {
        // 外部キー 'product_category_id' を使用して ProductCategory モデルと関連付けます。
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
}
