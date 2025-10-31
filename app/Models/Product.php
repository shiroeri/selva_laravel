<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ProductSubcategory; 
use App\Models\ProductCategory; 
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    public function member(): BelongsTo // 型ヒント追加
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
    
    /**
     * Product は複数のレビューを持つ (1対多)
     * ★修正しました: ProductReview::class から Review::class に戻します。★
     *
     * @return HasMany
     */
    public function reviews(): HasMany
    {
        // コントローラーで使用している App\Models\Review::class を指定します。
        // ProductReview::class ではありません。
        return $this->hasMany(Review::class, 'product_id');
    }

    /**
     * リレーションシップ: この商品が属するサブカテゴリ
     */
    public function subcategory(): BelongsTo // 型ヒント追加
    {
        // 外部キー 'product_subcategory_id' を使用して ProductSubcategory モデルと関連付けます。
        return $this->belongsTo(ProductSubcategory::class, 'product_subcategory_id');
    }

    /**
     * リレーションシップ: この商品が属するカテゴリ (大)
     */
    public function category(): BelongsTo // 型ヒント追加
    {
        // 外部キー 'product_category_id' を使用して ProductCategory モデルと関連付けます。
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    /**
     * アクセサ: 商品の画像URLを生成します。
     * ビューで $product->image_url として使用され、画像が表示されない問題を解決します。
     * @return string
     */
    public function getImageUrlAttribute(): string
    {
        // image_1 カラムが設定されているか確認
        if ($this->image_1) {
            // storage/products/xxx.jpg のようなパスを想定
            return asset('storage/' . $this->image_1);
        }
        
        // 画像パスが設定されていない場合は、商品名に基づいたダミー画像URLを生成
        $hash = substr(md5($this->name ?? 'default'), 0, 6);
        return "https://placehold.co/80x80/{$hash}/ffffff?text=Product";
    }
}
