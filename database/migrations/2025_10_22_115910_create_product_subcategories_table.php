<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_subcategories', function (Blueprint $table) {
            // id: int, NULL: いいえ, その他: AUTO_INCREMENT, コメント: サブカテゴリID
            $table->increments('id')->comment('サブカテゴリID');

            // product_category_id: int, NULL: いいえ, コメント: カテゴリID
            $table->integer('product_category_id')->comment('カテゴリID'); 
            
            // name: varchar(255), NULL: いいえ, コメント: サブカテゴリ名
            $table->string('name', 255)->comment('サブカテゴリ名'); 
            
            // タイムスタンプにコメントを付与
            $table->timestamp('created_at')->nullable()->comment('登録日時');
            $table->timestamp('updated_at')->nullable()->comment('編集日時');
            $table->timestamp('deleted_at')->nullable()->comment('削除日時'); // 論理削除用
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_subcategories');
    }
};