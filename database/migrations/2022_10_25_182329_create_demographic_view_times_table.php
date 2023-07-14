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
        Schema::create('demographic_view_times', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('user_id')->nullable();
			$table->unsignedBigInteger('post_id');
			$table->enum('type', ['single', 'battle'])->default('single');
			$table->unsignedBigInteger('time');
            $table->string('country')->nullable();
			$table->integer('birth_year')->nullable();
			$table->enum('sex', ['man', 'woman', 'non-binary'])->nullable();
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
        Schema::dropIfExists('demographic_view_times');
    }
};
