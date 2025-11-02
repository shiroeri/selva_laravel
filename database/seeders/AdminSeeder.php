<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 既に存在するレコードを重複登録しないようにチェック
        if (DB::table('administers')->where('login_id', 'admin')->exists()) {
            echo "Admin user 'admin' already exists. Skipping insertion.\n";
            return;
        }

        // 管理者アカウントを1件登録する
        DB::table('administers')->insert([
            'name' => 'しろたに えりか', // 氏名
            'login_id' => 'shirotani',      // ログインID (画像で指定されたlogin_idカラムに対応)
            'password' => Hash::make('shiroeri'), // パスワードを必ずハッシュ化して保存
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "Admin user 'admin' created successfully!\n";
    }
}
