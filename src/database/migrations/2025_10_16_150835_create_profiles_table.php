<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
/**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id(); // 主キー(PK)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // usersテーブルへの外部キー(FK)
            $table->string('image_path')->nullable(); // NULLを許容
            $table->string('post_code');
            $table->string('address');
            $table->string('building')->nullable(); // NULLを許容
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
        Schema::dropIfExists('profiles');
    }
}
