<?php

// 購入履歴用テーブル

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 既存のpurchaseテーブルにstatusカラムを追加
        Schema::table('purchases', function (Blueprint $table) {

            // 取引ステータス（デフォルトは0）
            // after-> purchaseテーブルのshipping_buildingカラムの後に追加する
            // （機能的には動くが、DBを見たときに関連する情報（配送先情報の近くなど）がまとまっていると、管理しやすくなる）
            $table->integer('status') ->default(0)->after('shipping_building');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {

            // ロールバック時に追加したカラムを消す
            $table->dropColumn('status');

        });
    }
}
