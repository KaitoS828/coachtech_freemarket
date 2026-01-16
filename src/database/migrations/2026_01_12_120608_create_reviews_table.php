<?php

// レビュー用テーブル

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete(); //どの取引のレビューか
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); //誰
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->integer('rate'); //評価、1-5
            $table->text('comment')->nullable(); //コメント
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
