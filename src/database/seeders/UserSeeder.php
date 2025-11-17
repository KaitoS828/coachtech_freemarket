<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 4. runメソッドの中身をこれに書き換える
        $now = Carbon::now();

        DB::table('users')->insert([
            [
                'name' => 'テストユーザー1',
                'email' => 'test1@example.com',
                'password' => Hash::make('password'), // 5. パスワードをハッシュ化
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'テストユーザー2',
                'email' => 'test2@example.com',
                'password' => Hash::make('password'),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'テストユーザー3',
                'email' => 'test3@example.com',
                'password' => Hash::make('password'),
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
