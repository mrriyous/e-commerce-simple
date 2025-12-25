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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 70);
            $table->string('slug', 70)->index();
            $table->text('description')->nullable();
            $table->tinyInteger('is_active')->default(1)->index();
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
        Schema::dropIfExists('categories');
    }
};
