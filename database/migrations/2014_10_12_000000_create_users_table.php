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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('phone_code')->nullable();
            $table->string('phone')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
			
            $table->string('google_id')->unique()->nullable();
            $table->timestamp('google_verified_at')->nullable();
			
            $table->string('apple_id')->unique()->nullable();
            $table->timestamp('apple_id_verified_at')->nullable();
			
            $table->string('facebook_id')->unique()->nullable();
            $table->timestamp('facebook_verified_at')->nullable();
			
            $table->string('password')->nullable();
            $table->enum('access_level', ['guest','unverified', 'verified', 'banned', 'restricted'])->default('guest');
			$table->boolean('official_badge')->default(0);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
