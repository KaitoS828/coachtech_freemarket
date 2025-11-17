<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        DB::table('items')->insert([
            [
                'user_id' => 1,
                'name' => '腕時計',
                'price' => 15000,
                'brand_name' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'condition' => '良好',
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'user_id' => 2,
                'name' => 'HDD',
                'price' => 5000,
                'brand_name' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'condition' => '目立った傷や汚れなし',
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'user_id' => 1,
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand_name' => null,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'condition' => 'やや傷や汚れあり',
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'user_id' => 1,
                'name' => '革靴',
                'price' => 4000,
                'brand_name' => null,
                'description' => 'クラシックなデザインの革靴',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'condition' => '状態が悪い',
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'user_id' => 2,
                'name' => 'ノートPC',
                'price' => 45000,
                'brand_name' => null,
                'description' => '高性能なノートパソコン',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'condition' => '良好',
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'user_id' => 3,
                'name' => 'マイク',
                'price' => 8000,
                'brand_name' => null,
                'description' => '高音質のレコーディング用マイク',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'condition' => '目立った傷や汚れなし',
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'user_id' => 1,
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand_name' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'condition' => 'やや傷や汚れあり',
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'user_id' => 2,
                'name' => 'タンブラー',
                'price' => 500,
                'brand_name' => null,
                'description' => '使いやすいタンブラー',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'condition' => '状態が悪い',
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'user_id' => 3,
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand_name' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'condition' => '良好',
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'user_id' => 1,
                'name' => 'メイクセット',
                'price' => 2500,
                'brand_name' => null,
                'description' => '便利なメイクアップセット',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'condition' => '目立った傷や汚れなし',
                'created_at' => $now, 'updated_at' => $now
            ],
        ]);

        DB::table('category_item')->insert([
            ['item_id' => 1, 'category_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['item_id' => 1, 'category_id' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['item_id' => 1, 'category_id' => 12, 'created_at' => $now, 'updated_at' => $now],
            ['item_id' => 2, 'category_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['item_id' => 3, 'category_id' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['item_id' => 4, 'category_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['item_id' => 4, 'category_id' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['item_id' => 5, 'category_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['item_id' => 6, 'category_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['item_id' => 6, 'category_id' => 8, 'created_at' => $now, 'updated_at' => $now],
            ['item_id' => 7, 'category_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['item_id' => 7, 'category_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['item_id' => 8, 'category_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['item_id' => 8, 'category_id' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['item_id' => 9, 'category_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['item_id' => 9, 'category_id' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['item_id' => 10, 'category_id' => 6, 'created_at' => $now, 'updated_at' => $now],

        ]);
    }
}
