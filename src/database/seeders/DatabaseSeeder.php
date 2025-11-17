<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $this->call([
            CategorySeeder::class,//先にカテゴリーを追加
            UserSeeder::class,//次にユーザーを追加
            ProfileSeeder::class,//プロフィール作成
            ItemSeeder::class,//商品を追加
        ]);
    }
}