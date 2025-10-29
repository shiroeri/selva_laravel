<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    // テーブル名
    protected $table = 'reviews';

    // 複数代入可能な属性
    protected $fillable = [
        'member_id',
        'product_id',
        'evaluation',
        'comment',
    ];

    /**
     * このレビューを投稿した会員を取得
     */
    public function member(): BelongsTo
    {
        // member_idでmembersテーブル（またはUserテーブル）を参照すると仮定
        // ここでは仮にMemberモデルを参照させます
        return $this->belongsTo(Member::class, 'member_id');
    }

    /**
     * このレビューが関連付けられている商品を取得
     */
    public function product(): BelongsTo
    {
        // product_idでproductsテーブルを参照
        return $this->belongsTo(Product::class, 'product_id');
    }
}
