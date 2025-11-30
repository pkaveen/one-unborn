<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('link_monitoring_data')) {
            return;
        }

        Schema::create('link_monitoring_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_id')->constrained('client_links')->onDelete('cascade');
            $table->timestamp('timestamp')->useCurrent();
            $table->unsignedBigInteger('rx_bytes')->default(0);
            $table->unsignedBigInteger('tx_bytes')->default(0);
            $table->unsignedBigInteger('rx_packets')->default(0);
            $table->unsignedBigInteger('tx_packets')->default(0);
            $table->unsignedBigInteger('rx_errors')->default(0);
            $table->unsignedBigInteger('tx_errors')->default(0);
            $table->decimal('latency_ms', 10, 2)->nullable();
            $table->decimal('packet_loss_percent', 5, 2)->nullable();
            $table->enum('link_status', ['up', 'down'])->default('up');
            $table->decimal('rx_rate_mbps', 10, 2)->nullable();
            $table->decimal('tx_rate_mbps', 10, 2)->nullable();

            $table->index(['link_id', 'timestamp']);
            $table->index('timestamp');
            $table->index('link_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('link_monitoring_data');
    }
};
