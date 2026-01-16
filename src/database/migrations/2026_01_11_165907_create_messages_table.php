<?php

// チャット用テーブル

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete(); //どの取引のチャットか(cascadeOnDelete: 取引が消えたらメッセージも消える)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); //誰
            $table->text('content')->nullable(); //内容、文字数はバリデーション
            $table->string('image_path')->nullable(); //画像
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
        Schema::dropIfExists('messages');
    }
}
