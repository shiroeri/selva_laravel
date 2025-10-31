<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes; // SoftDeletesトレイトをインポート

class ProductReview extends Model
{
    // SoftDeletesトレイトを使用することで、delete()実行時にdeleted_atが設定される
    use HasFactory, SoftDeletes; 

    // ★重要★
    // 実際のテーブル名が 'reviews' であるため、テーブル名を明示的に指定します。
    protected $table = 'reviews';

    // 以下のカラム名がデータベースと一致していることを前提とします
    // 'product_id', 'member_id', 'evaluation', 'comment'

    protected $fillable = [
        'product_id',
        'member_id',
        'evaluation', // 評価のカラム名
        'comment',    // コメントのカラム名
    ];

    /**
     * このレビューが関連付けられている商品を取得
     */
    public function product(): BelongsTo
    {
        // reviewsテーブルの product_id と productsテーブルの id を紐づけ
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
