<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('client_links')) {
            return;
        }

        Schema::create('client_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deliverable_id')->constrained('deliverables')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('router_id')->constrained('mikrotik_routers')->onDelete('cascade');
            $table->string('interface_name')->comment('e.g., ether1, pppoe-out1');
            $table->string('circuit_id')->nullable();
            $table->string('link_name');
            $table->integer('committed_speed_mbps');
            $table->decimal('committed_sla_uptime', 5, 2)->default(99.50)->comment('Percentage e.g., 99.50%');
            $table->integer('committed_sla_latency_ms')->default(50);
            $table->decimal('committed_sla_packet_loss', 5, 2)->default(1.00)->comment('Percentage e.g., 1.00%');
            $table->date('activation_date');
            $table->boolean('is_active')->default(true);
            $table->string('grafana_dashboard_uid')->nullable()->comment('Grafana dashboard UID for iframe');
            $table->text('monitoring_config')->nullable()->comment('JSON config for monitoring');
            $table->timestamps();

            $table->index('client_id');
            $table->index('router_id');
            $table->index('deliverable_id');
            $table->index('is_active');
            $table->index('interface_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_links');
    }
};
