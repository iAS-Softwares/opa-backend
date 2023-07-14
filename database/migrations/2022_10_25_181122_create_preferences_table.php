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
        Schema::create('preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
			$table->json('selection')->default(json_encode([]));
			$table->json('recommendation')->default(json_encode([]));
			$table->json('viewtime')->default(json_encode([]));
			$table->json('likes')->default(json_encode([]));
			$table->json('follows')->default(json_encode([]));
			$table->json('average')->default(json_encode([]));
			$table->unsignedBigInteger('category_id')->nullable();
            $table->timestamps();
			
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('category_id')->references('id')->on('preference_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preferences');
    }
};
