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
        
        Schema::create('sign_up_requests', function (Blueprint $table) {
            $table->id();
			
			$table->string('ticket');
			            
			$table->string('phone_code')->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('email')->unique()->nullable();
			$table->integer('retry_count')->default(0);
			
			$table->string('phone_otp')->nullable();
			$table->dateTime('phone_otp_start_at', $precision = 0)->nullable();
			$table->integer('phone_otp_count')->default(1);
			
            $table->string('email_otp')->nullable();
			$table->dateTime('email_otp_start_at', $precision = 0)->nullable();
            $table->integer('email_otp_count')->default(1);
			
			$table->enum('status', ['processing', 'failed', 'suspicious'])->default('processing');
            
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
        Schema::dropIfExists('sign_up_requests');
    }
};
