<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductReview extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * 実テーブル名（仕様：reviews）
     */
    protected $table = 'reviews';

    /**
     * 代入可能カラム
     */
    protected $fillable = [
        'product_id',
        'member_id',
        'evaluation', // 評価
        'comment',    // コメント
    ];

    /**
     * このレビューの対象商品
     */
    public function product(): BelongsTo
    {
        // reviews.product_id -> products.id
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * このレビューを投稿した会員
     */
    public function member(): BelongsTo
    {
        // reviews.member_id -> members.id
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }
}
