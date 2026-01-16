<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        DB::table('categories')->insert([
            ['name' => 'ファッション', 'created_at' => $now, 'updated_at' => $now],//ID1
            ['name' => '家電', 'created_at' => $now, 'updated_at' => $now],//ID2
            ['name' => '食品', 'created_at' => $now, 'updated_at' => $now],//ID3
            ['name' => 'インテリア', 'created_at' => $now, 'updated_at' => $now],//ID4
            ['name' => 'レディース', 'created_at' => $now, 'updated_at' => $now],//ID5
            ['name' => 'メンズ', 'created_at' => $now, 'updated_at' => $now],//ID6
            ['name' => 'コスメ', 'created_at' => $now, 'updated_at' => $now],//ID7
            ['name' => '本', 'created_at' => $now, 'updated_at' => $now],//ID8
            ['name' => 'ゲーム', 'created_at' => $now, 'updated_at' => $now],//ID9
            ['name' => 'スポーツ', 'created_at' => $now, 'updated_at' => $now],//ID10
            ['name' => 'キッチン', 'created_at' => $now, 'updated_at' => $now],//ID11
            ['name' => 'ハンドメイド', 'created_at' => $now, 'updated_at' => $now],//ID12
            ['name' => 'アクセサリー', 'created_at' => $now, 'updated_at' => $now],//ID13
            ['name' => 'おもちゃ', 'created_at' => $now, 'updated_at' => $now],//ID14
            ['name' => 'ベビー・キッズ', 'created_at' => $now, 'updated_at' => $now],//ID15
        ]);
    }
}
