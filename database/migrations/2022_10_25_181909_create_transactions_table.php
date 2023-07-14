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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('user_id');
			$table->string('transaction_id');
			$table->string('currency');
			$table->unsignedDecimal('amount', $precision = 8, $scale = 2);
			$table->unsignedDecimal('tax', $precision = 8, $scale = 2);
			$table->unsignedDecimal('total', $precision = 8, $scale = 2);
			$table->unsignedDecimal('conversion', $precision = 8, $scale = 2);
			$table->boolean('received')->default(false);
			$table->boolean('failed')->default(true);
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
        Schema::dropIfExists('transactions');
    }
};
