<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add portal authentication columns to existing clients table
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'portal_username')) {
                $table->string('portal_username')->nullable()->unique()->after('support_spoc_email');
            }
            if (!Schema::hasColumn('clients', 'portal_password')) {
                $table->string('portal_password')->nullable()->after('portal_username');
            }
            if (!Schema::hasColumn('clients', 'portal_active')) {
                $table->boolean('portal_active')->default(false)->after('portal_password');
            }
            if (!Schema::hasColumn('clients', 'portal_last_login')) {
                $table->timestamp('portal_last_login')->nullable()->after('portal_active');
            }
            if (!Schema::hasColumn('clients', 'remember_token')) {
                $table->rememberToken()->after('portal_last_login');
            }
        });

        // Add indexes for portal authentication
        Schema::table('clients', function (Blueprint $table) {
            $indexExists = collect(Schema::getConnection()
                ->getDoctrineSchemaManager()
                ->listTableIndexes('clients'))
                ->has('clients_portal_active_index');
                
            if (!$indexExists) {
                $table->index('portal_active', 'clients_portal_active_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex('clients_portal_active_index');
            $table->dropColumn([
                'portal_username',
                'portal_password',
                'portal_active',
                'portal_last_login',
                'remember_token'
            ]);
        });
    }
};
