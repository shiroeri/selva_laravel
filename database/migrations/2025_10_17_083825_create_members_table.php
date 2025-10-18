<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            // 会員ID (id)
            $table->id('id')->comment('会員ID');

            // 氏名（姓） (name_sei)
            $table->string('name_sei', 255)->comment('氏名（姓）');

            // 氏名（名） (name_mei)
            $table->string('name_mei', 255)->comment('氏名（名）');

            // ニックネーム (nickname)
            $table->string('nickname', 255)->comment('ニックネーム');

            // 性別 (gender)
            $table->integer('gender')->comment('性別（1=男性、2=女性）');

            // パスワード (password)
            $table->string('password', 255)->comment('パスワード');

            // メールアドレス (email)
            $table->string('email', 255)->unique()->comment('メールアドレス');
            // ※ メールアドレスは通常一意（UNIQUE）であるべきなので追加しました

            // 認証コード (auth_code)
            $table->integer('auth_code')->nullable()->comment('認証コード');

            // タイムスタンプ (created_at, updated_at, deleted_at)
            $table->timestamps(); // created_at と updated_at を NULL許容で自動作成
            $table->softDeletes(); // deleted_at (論理削除用) を NULL許容で自動作成
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};