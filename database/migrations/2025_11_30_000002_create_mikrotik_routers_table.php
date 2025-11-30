<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mikrotik_routers')) {
            return;
        }

        Schema::create('mikrotik_routers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('management_ip', 45)->unique();
            $table->integer('api_port')->default(8728);
            $table->string('api_username');
            $table->text('api_password'); // Encrypted
            $table->boolean('use_ssl')->default(false);
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_poll')->nullable();
            $table->enum('status', ['online', 'offline', 'unreachable'])->default('offline');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('management_ip');
            $table->index('is_active');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mikrotik_routers');
    }
};
