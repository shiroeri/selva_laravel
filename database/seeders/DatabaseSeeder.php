<?php

namespace Database\Seeders;

// ★追加: 必要なシーダークラスをuseします★
use Database\Seeders\ProductCategorySeeder; 
use Database\Seeders\ProductSubcategorySeeder; // ★ProductSubcategorySeederを追加
use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ★修正点1: デフォルトのUser::factory() 呼び出しはコメントアウト（または削除）します。
        // User::factory(10)->create(); 

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // ★修正点2: ご自身のシーダーを呼び出すようにします。
        $this->call([
            ProductCategorySeeder::class, // product_categoriesテーブルにデータを投入
            ProductSubcategorySeeder::class, // product_subcategoriesテーブルにデータを投入
            AdminSeeder::class, // administersテーブルにデータを投入
            // 必要に応じて、MemberSeederなどをここに追加します。
        ]);
    }
}
