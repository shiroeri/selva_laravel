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
        // STEP 1: テーブルの基本構造（カラム定義）のみを作成
        // このブロックでは外部キー制約は定義せず、参照先の型に合わせたカラムを定義
        Schema::create('reviews', function (Blueprint $table) {
            // id: int, NOT NULL, AUTO_INCREMENT (コメントID)
            $table->increments('id')->comment('コメントID');

            // member_id: unsignedInteger に修正 (members.id の型に合わせるため)
            $table->unsignedInteger('member_id')->comment('会員ID');

            // product_id: unsignedInteger に修正 (products.id の型に合わせるため)
            $table->unsignedInteger('product_id')->comment('商品ID');

            // evaluation: int, NOT NULL (評価)
            $table->tinyInteger('evaluation')->comment('評価');

            // comment: text, NOT NULL (商品コメント)
            $table->text('comment')->comment('商品コメント');

            // タイムスタンプ
            $table->timestamp('created_at')->nullable()->comment('登録日時');
            $table->timestamp('updated_at')->nullable()->comment('編集日時');
            $table->timestamp('deleted_at')->nullable()->comment('削除日時'); 
        });
        
        // STEP 2: 外部キー制約を別の Schema::table ブロックで追加
        Schema::table('reviews', function (Blueprint $table) {
            
            // 1. 会員ID (member_id) の外部キー制約
            $table->foreign('member_id')
                  ->references('id')
                  ->on('members')
                  ->onDelete('cascade');

            // 2. 商品ID (product_id) の外部キー制約
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }

    /**
     * マイグレーションを元に戻す
     *
     * @return void
     */
    public function down()
    {
        // 外部キー制約を削除してからテーブルを削除
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                // 外部キーを削除
                $table->dropForeign(['member_id']);
                $table->dropForeign(['product_id']);
            });
        }
        
        Schema::dropIfExists('reviews');
    }
};
