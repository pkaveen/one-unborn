<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First drop the existing column
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        // Then add the new column with Active/Inactive enum
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('contract_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->enum('status', ['Draft', 'Submitted', 'Approved', 'Cancelled'])->default('Draft')->after('contract_period');
        });
    }
};
