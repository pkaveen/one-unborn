<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sla_reports')) {
            return;
        }

        Schema::create('sla_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_id')->constrained('client_links')->onDelete('cascade');
            $table->date('report_month')->comment('First day of month');
            $table->integer('total_minutes');
            $table->integer('uptime_minutes');
            $table->integer('downtime_minutes');
            $table->decimal('uptime_percentage', 5, 2);
            $table->decimal('avg_latency_ms', 10, 2)->nullable();
            $table->decimal('avg_packet_loss', 5, 2)->nullable();
            $table->decimal('max_latency_ms', 10, 2)->nullable();
            $table->decimal('max_packet_loss', 5, 2)->nullable();
            $table->boolean('sla_met');
            $table->text('sla_breach_details')->nullable()->comment('JSON: reasons for breach');
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamps();

            $table->unique(['link_id', 'report_month']);
            $table->index('report_month');
            $table->index('sla_met');
            $table->index('link_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sla_reports');
    }
};
