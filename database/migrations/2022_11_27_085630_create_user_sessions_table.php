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
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
			
            $table->string('token', 64)->unique();
            $table->string('ip_address', 45)->nullable();
            $table->string('mac_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('user_id');
			
            $table->timestamps();
			
			
			
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_sessions');
    }
};
