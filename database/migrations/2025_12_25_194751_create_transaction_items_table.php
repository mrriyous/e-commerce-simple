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
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id')->index();
            $table->unsignedBigInteger('product_id')->index(); // Reference to product, but we cache data
            $table->string('product_name'); // Cached product name
            $table->string('product_image_url')->nullable(); // Cached product image
            $table->integer('quantity');
            $table->decimal('price_at_time', 10, 2); // Price when transaction was made
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
            $table->softDeletes()->index();
            $table->index('created_at');
            
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
