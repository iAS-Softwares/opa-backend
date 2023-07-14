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
        Schema::create('preference_categories', function (Blueprint $table) {
            $table->id();
			$table->json('average')->default(json_encode([]));
			$table->json('post_singles')->default(json_encode([]));
			$table->json('post_battles')->default(json_encode([]));
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
        Schema::dropIfExists('preference_categories');
    }
};
