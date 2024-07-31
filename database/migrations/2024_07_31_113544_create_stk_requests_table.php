<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stk_requests', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->double('amount', 8,2);
            $table->string('reference');
            $table->string('description');
            $table->string('status');
            $table->string('checkoutRequestID')->unique();
            $table->string('merchantRequestID')->unique();
            $table->string('responseCode')->nullable();
            $table->string('MpesaReceiptNumber')->nullable();
            $table->string('ResultDesc')->nullable();
            $table->string('TransactionDate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stk_requests');
    }
};
