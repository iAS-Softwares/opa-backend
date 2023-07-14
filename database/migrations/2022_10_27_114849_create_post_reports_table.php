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
        Schema::create('post_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('report_id');
			$table->string('description')->nullable();
			$table->enum('type', ['single', 'battle', 'user'])->default('battle');
            $table->timestamps();
			
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('report_id')->references('id')->on('report_types')->onDelete('cascade');
			
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_reports');
    }
};
