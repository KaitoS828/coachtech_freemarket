<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 3. runメソッドの中身をこれに書き換える
        $now = Carbon::now();

        DB::table('profiles')->insert([
            [
                'user_id' => 1, // ユーザーID: 1 のプロフィール
                'image_path' => null,
                'post_code' => '100-0001',
                'address' => '東京都千代田区千代田1-1',
                'building' => '皇居',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id' => 2, // ユーザーID: 2 のプロフィール
                'image_path' => null,
                'post_code' => '530-0001',
                'address' => '大阪府大阪市北区梅田3-1-1',
                'building' => '大阪駅',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id' => 3, // ユーザーID: 3 のプロフィール
                'image_path' => null,
                'post_code' => '812-0012',
                'address' => '福岡県福岡市博多区博多駅中央街1-1',
                'building' => '博多駅',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
