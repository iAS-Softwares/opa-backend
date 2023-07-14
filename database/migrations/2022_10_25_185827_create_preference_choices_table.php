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
        Schema::create('preference_choices', function (Blueprint $table) {
            $table->id();
			$table->string('name');
			$table->string('slug')->unique();
			$table->unsignedBigInteger('brand_id')->nullable();
			$table->unsignedBigInteger('tag_id')->nullable();
			$table->unsignedBigInteger('user_id')->nullable();
			$table->boolean('visiblity')->default(0);
			$table->boolean('premium')->default(0);
			$table->unsignedDecimal('price', $precision = 8, $scale = 2)->default(0);
            $table->timestamps();
			
			$table->foreign('brand_id')->references('id')->on('brands');
			$table->foreign('tag_id')->references('id')->on('tags');
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
        Schema::dropIfExists('preference_choices');
    }
};
