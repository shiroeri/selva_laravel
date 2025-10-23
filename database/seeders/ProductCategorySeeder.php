<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存のデータをすべてクリア
        DB::table('product_categories')->truncate();
        
        // 挿入するデータ
        $categories = [
            // id: 1
            ['id' => 1, 'name' => 'インテリア'],
            // id: 2
            ['id' => 2, 'name' => '家電'],
            // id: 3
            ['id' => 3, 'name' => 'ファッション'],
            // id: 4
            ['id' => 4, 'name' => '美容'],
            // id: 5
            ['id' => 5, 'name' => '本・雑誌'],
        ];

        // タイムスタンプを付与
        $now = Carbon::now();
        foreach ($categories as &$category) {
            $category['created_at'] = $now;
            $category['updated_at'] = $now;
        }
        
        // データベースに挿入
        DB::table('product_categories')->insert($categories);
    }
}
