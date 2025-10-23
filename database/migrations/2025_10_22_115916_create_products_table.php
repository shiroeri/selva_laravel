<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id')->comment('スレッドID'); // id: int, NULL: いいえ, その他: AUTO_INCREMENT, コメント: スレッドID

            // 外部キー (会員ID, カテゴリID, サブカテゴリID)
            $table->integer('member_id')->comment('会員ID'); // member_id: int, NULL: いいえ
            $table->integer('product_category_id')->comment('カテゴリID'); // product_category_id: int, NULL: いいえ
            $table->integer('product_subcategory_id')->comment('サブカテゴリID'); // product_subcategory_id: int, NULL: いいえ

            $table->string('name', 255)->comment('商品名'); // name: varchar(255), NULL: いいえ
            
            // 写真カラム (NULLを許容)
            $table->string('image_1', 255)->nullable()->comment('写真 1'); // image_1: varchar(255), NULL: はい
            $table->string('image_2', 255)->nullable()->comment('写真 2'); // image_2: varchar(255), NULL: はい
            $table->string('image_3', 255)->nullable()->comment('写真 3'); // image_3: varchar(255), NULL: はい
            $table->string('image_4', 255)->nullable()->comment('写真 4'); // image_4: varchar(255), NULL: はい

            $table->text('product_content')->comment('商品説明'); // product_content: text, NULL: いいえ

            $table->timestamp('created_at')->nullable()->comment('登録日時');
            
            // updated_at (編集日時)
            $table->timestamp('updated_at')->nullable()->comment('編集日時');
            
            // deleted_at (削除日時)
            $table->timestamp('deleted_at')->nullable()->comment('削除日時'); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};