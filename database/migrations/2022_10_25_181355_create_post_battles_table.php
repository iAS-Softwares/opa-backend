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
        Schema::create('post_battles', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('user_id');
			$table->unsignedBigInteger('post_id1');
			$table->unsignedBigInteger('post_id2');
			$table->bigInteger('total_count')->default(0);
			$table->string('caption')->default('');
			$table->json('tags')->default(json_encode([]));
			$table->json('brands')->default(json_encode([]));
			$table->boolean('visiblity')->default(true);
			$table->boolean('annonymous')->default(false);
			$table->boolean('banned')->default(false);
			$table->json('computed_preference')->default(json_encode([]));
            $table->timestamps();
			
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('post_id1')->references('id')->on('post_singles');
			$table->foreign('post_id2')->references('id')->on('post_singles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_battles');
    }
};
