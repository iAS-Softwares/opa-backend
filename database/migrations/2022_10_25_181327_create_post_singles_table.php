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
        Schema::create('post_singles', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('user_id');
			$table->unsignedBigInteger('image_id');
			$table->json('brands')->default(json_encode([]));
			$table->unsignedBigInteger('count')->default(0);
			$table->string('caption')->default('');
			$table->json('tags')->default(json_encode([]));
			$table->boolean('visiblity')->default(true);
			$table->boolean('banned')->default(false);
			$table->json('computed_preference')->default(json_encode([]));
            $table->timestamps();
			
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('image_id')->references('id')->on('images');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_singles');
    }
};
