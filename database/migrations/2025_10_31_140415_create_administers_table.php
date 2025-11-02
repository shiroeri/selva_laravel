<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * マイグレーションを実行する。
     * 実行コマンド: php artisan migrate
     */
    public function up(): void
    {
        // administersテーブルを作成
        Schema::create('administers', function (Blueprint $table) {
            // id: int, AUTO_INCREMENT, コメント: 管理者ID (Laravel標準のプライマリキー)
            $table->increments('id')->comment('管理者ID'); 

            // name: varchar(255), NULL: いいえ, コメント: 氏名
            $table->string('name', 255)->comment('氏名');

            // login_id: varchar(255), NULL: いいえ, unique, コメント: ログインID
            // ※ ログインIDは一意制約（unique）を設定しています
            $table->string('login_id', 255)->unique()->comment('ログインID');

            // password: varchar(255), NULL: いいえ, コメント: パスワード
            $table->string('password', 255)->comment('パスワード');

            // タイムスタンプ定義 (ご指定の内容)
            // created_at: timestamp, NULL: はい, デフォルト値: NULL, コメント: 登録日時
            $table->timestamp('created_at')->nullable()->comment('登録日時');
            
            // updated_at: timestamp, NULL: はい, デフォルト値: NULL, コメント: 編集日時
            $table->timestamp('updated_at')->nullable()->comment('編集日時');
            
            // deleted_at: timestamp, NULL: はい, デフォルト値: NULL, コメント: 削除日時
            $table->timestamp('deleted_at')->nullable()->comment('削除日時'); 
        });
    }

    /**
     * マイグレーションを元に戻す。
     * 実行コマンド: php artisan migrate:rollback
     */
    public function down(): void
    {
        // administersテーブルを削除
        Schema::dropIfExists('administers');
    }
};
