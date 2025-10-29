<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * マイグレーションを実行
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            // id: int, NOT NULL, AUTO_INCREMENT (コメントID)
            $table->increments('id')->comment('コメントID');

            // member_id: int, NOT NULL (会員ID) - 外部キー設定のためunsignedBigIntegerを使用
            $table->unsignedBigInteger('member_id')->comment('会員ID');

            // product_id: int, NOT NULL (商品ID) - 外部キー設定のためunsignedBigIntegerを使用
            $table->unsignedBigInteger('product_id')->comment('商品ID');

            // evaluation: int, NOT NULL (評価)
            $table->tinyInteger('evaluation')->comment('評価');

            // comment: text, NOT NULL (商品コメント)
            $table->text('comment')->comment('商品コメント');

            // タイムスタンプ
            $table->timestamp('created_at')->nullable()->comment('登録日時');
            $table->timestamp('updated_at')->nullable()->comment('編集日時');
            $table->timestamp('deleted_at')->nullable()->comment('削除日時'); 
            
            // ----------------------------------------------------
            // 外部キー制約の定義
            // ----------------------------------------------------

            // 1. 会員ID (member_id)
            // membersテーブルのidカラムを参照します。
            // 参照元が削除された場合、レビューレコードも削除されます (onDelete('cascade'))。
            $table->foreign('member_id')
                  ->references('id')
                  ->on('members')
                  ->onDelete('cascade');

            // 2. 商品ID (product_id)
            // productsテーブルのidカラムを参照します。
            // 参照元が削除された場合、レビューレコードも削除されます (onDelete('cascade'))。
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');

            // 検索性能向上のための複合インデックス
            $table->index(['member_id', 'product_id']);
        });
    }

    /**
     * マイグレーションを元に戻す
     *
     * @return void
     */
    public function down()
    {
        // 外部キー制約を削除してからテーブルを削除するのが安全です。
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropForeign(['product_id']);
        });
        
        Schema::dropIfExists('reviews');
    }
};
