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
        Schema::table('transaction_report_notif_processes', function (Blueprint $table) {
            $table->json('report_data')->nullable()->after('report_date');
            $table->decimal('total_sales', 10, 2)->nullable()->after('report_data');
            $table->integer('total_items')->nullable()->after('total_sales');
            $table->integer('total_transactions')->nullable()->after('total_items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_report_notif_processes', function (Blueprint $table) {
            $table->dropColumn(['report_data', 'total_sales', 'total_items', 'total_transactions']);
        });
    }
};

