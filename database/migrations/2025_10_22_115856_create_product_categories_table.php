<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            // ID
            $table->increments('id')->comment('カテゴリID');

            // カテゴリ名
            $table->string('name', 255)->comment('カテゴリ名'); 
            
            // ★★★ 修正箇所: タイムスタンプにコメントを追記 ★★★
            // created_at (登録日時)
            $table->timestamp('created_at')->nullable()->comment('登録日時');
            
            // updated_at (編集日時)
            $table->timestamp('updated_at')->nullable()->comment('編集日時');
            
            // deleted_at (削除日時)
            $table->timestamp('deleted_at')->nullable()->comment('削除日時'); // softDeletesの代わりにこちらを使用
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};