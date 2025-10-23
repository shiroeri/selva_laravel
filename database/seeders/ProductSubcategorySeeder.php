<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ProductSubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 挿入するデータ
        $subcategories = [
            // Category ID: 1 (インテリア)
            ['id' => 1, 'product_category_id' => 1, 'name' => '収納家具'],
            ['id' => 2, 'product_category_id' => 1, 'name' => '寝具'],
            ['id' => 3, 'product_category_id' => 1, 'name' => 'ソファ'],
            ['id' => 4, 'product_category_id' => 1, 'name' => 'ベッド'],
            ['id' => 5, 'product_category_id' => 1, 'name' => '照明'],
            
            // Category ID: 2 (家電)
            ['id' => 6, 'product_category_id' => 2, 'name' => 'テレビ'],
            ['id' => 7, 'product_category_id' => 2, 'name' => '掃除機'],
            ['id' => 8, 'product_category_id' => 2, 'name' => 'エアコン'],
            ['id' => 9, 'product_category_id' => 2, 'name' => '冷蔵庫'],
            ['id' => 10, 'product_category_id' => 2, 'name' => 'レンジ'],
            
            // Category ID: 3 (ファッション)
            ['id' => 11, 'product_category_id' => 3, 'name' => 'トップス'],
            ['id' => 12, 'product_category_id' => 3, 'name' => 'ボトム'],
            ['id' => 13, 'product_category_id' => 3, 'name' => 'ワンピース'],
            ['id' => 14, 'product_category_id' => 3, 'name' => 'ファッション小物'],
            ['id' => 15, 'product_category_id' => 3, 'name' => 'ドレス'],
            
            // Category ID: 4 (美容)
            ['id' => 16, 'product_category_id' => 4, 'name' => 'ネイル'],
            ['id' => 17, 'product_category_id' => 4, 'name' => 'アロマ'],
            ['id' => 18, 'product_category_id' => 4, 'name' => 'スキンケア'],
            ['id' => 19, 'product_category_id' => 4, 'name' => '香水'],
            ['id' => 20, 'product_category_id' => 4, 'name' => 'メイク'],

            // Category ID: 5 (本・雑誌)
            ['id' => 21, 'product_category_id' => 5, 'name' => '旅行'],
            ['id' => 22, 'product_category_id' => 5, 'name' => 'ホビー'],
            ['id' => 23, 'product_category_id' => 5, 'name' => '写真集'],
            ['id' => 24, 'product_category_id' => 5, 'name' => '小説'],
            ['id' => 25, 'product_category_id' => 5, 'name' => 'ライフスタイル'],
        ];

        // 実行前に既存のデータを全て削除し、IDをリセットする
        DB::table('product_subcategories')->truncate(); 

        // タイムスタンプを付与
        $now = Carbon::now();
        foreach ($subcategories as &$subcategory) {
            $subcategory['created_at'] = $now;
            $subcategory['updated_at'] = $now;
        }
        
        // データベースに挿入
        DB::table('product_subcategories')->insert($subcategories);
    }
}
