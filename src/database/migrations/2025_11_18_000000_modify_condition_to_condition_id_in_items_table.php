<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyConditionToConditionIdInItemsTable extends Migration
{
    /**
     * Run the migrations.
     * conditionカラム（文字列）をcondition_id（外部キー）に変更
     *
     * @return void
     */
    public function up()
    {
        // 1. 新しいcondition_idカラムを追加
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('condition_id')->nullable()->after('description')->constrained('conditions');
        });

        // 2. 既存データをマイグレーション（文字列からIDへ変換）
        DB::table('items')->where('condition', '良好')->update(['condition_id' => 1]);
        DB::table('items')->where('condition', '目立った傷や汚れなし')->update(['condition_id' => 2]);
        DB::table('items')->where('condition', 'やや傷や汚れあり')->update(['condition_id' => 3]);
        DB::table('items')->where('condition', '状態が悪い')->update(['condition_id' => 4]);

        // 3. 古いconditionカラムを削除
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('condition');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 1. conditionカラムを復元
        Schema::table('items', function (Blueprint $table) {
            $table->string('condition')->nullable()->after('description');
        });

        // 2. データを復元
        DB::table('items')->where('condition_id', 1)->update(['condition' => '良好']);
        DB::table('items')->where('condition_id', 2)->update(['condition' => '目立った傷や汚れなし']);
        DB::table('items')->where('condition_id', 3)->update(['condition' => 'やや傷や汚れあり']);
        DB::table('items')->where('condition_id', 4)->update(['condition' => '状態が悪い']);

        // 3. condition_idカラムを削除
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['condition_id']);
            $table->dropColumn('condition_id');
        });
    }
}
