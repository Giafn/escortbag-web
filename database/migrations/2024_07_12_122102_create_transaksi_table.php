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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->uuid('order_id')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('items');
            $table->json('buyer');
            $table->string('cart_ids');
            $table->integer('total');
            $table->enum('status', ['PENDING', 'SUCCESS', 'FAILED'])->default('PENDING');
            $table->text('url_pembayaran')->nullable();
            $table->string('snap_token')->nullable();
            $table->boolean('is_shipping')->default(false);
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
