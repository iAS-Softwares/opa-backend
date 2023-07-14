<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_boosts', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('user_id');
			$table->unsignedBigInteger('post_id');
			$table->enum('type', ['single', 'battle'])->default('single');
			$table->dateTime('starts_at', $precision = 0);
			$table->dateTime('expires_at', $precision = 0);
			$table->bigInteger('call_count')->default(0);
            $table->timestamps();
			
			$table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_boosts');
    }
};
