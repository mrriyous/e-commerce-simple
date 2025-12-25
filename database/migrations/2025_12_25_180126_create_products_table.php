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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->string('name', 100);
            $table->string('slug', 100)->unique()->index();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('sku', 50)->unique()->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->string('image_url')->nullable();
            $table->decimal('rating', 3, 2)->default(0)->index();
            $table->integer('reviews_count')->default(0);
            $table->tinyInteger('is_active')->default(1)->index();
            $table->tinyInteger('is_featured')->default(0)->index();
            $table->integer('sort_order')->nullable();
            $table->timestamps();
            $table->softDeletes()->index();
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
