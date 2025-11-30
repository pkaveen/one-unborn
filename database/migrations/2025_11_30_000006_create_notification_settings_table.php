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
        if (!Schema::hasTable('notification_settings')) {
            Schema::create('notification_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
                
                // SLA Notifications
                $table->boolean('sla_breach_enabled')->default(true);
                $table->text('sla_breach_recipients')->nullable(); // JSON array of emails
                $table->boolean('sla_breach_to_client')->default(true);
                $table->boolean('sla_breach_to_operations')->default(true);
                
                // Real-time Alerts
                $table->boolean('link_down_enabled')->default(true);
                $table->integer('link_down_threshold_minutes')->default(5); // Alert after X minutes down
                $table->text('link_down_recipients')->nullable(); // JSON array of emails
                
                $table->boolean('high_latency_enabled')->default(true);
                $table->integer('high_latency_threshold_ms')->default(50); // Alert when latency > X ms
                $table->integer('high_latency_duration_minutes')->default(10); // Sustained for X minutes
                $table->text('high_latency_recipients')->nullable();
                
                $table->boolean('high_packet_loss_enabled')->default(true);
                $table->decimal('high_packet_loss_threshold_percent', 5, 2)->default(2.0);
                $table->integer('high_packet_loss_duration_minutes')->default(10);
                $table->text('high_packet_loss_recipients')->nullable();
                
                // WhatsApp Notifications
                $table->boolean('whatsapp_enabled')->default(false);
                $table->text('whatsapp_numbers')->nullable(); // JSON array of phone numbers
                
                // Email Settings
                $table->boolean('email_enabled')->default(true);
                $table->string('email_from')->nullable();
                
                // Alert Cooldown (prevent spam)
                $table->integer('alert_cooldown_minutes')->default(30);
                
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('notification_logs')) {
            Schema::create('notification_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_link_id')->nullable()->constrained('client_links')->onDelete('cascade');
                $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
                $table->string('notification_type'); // sla_breach, link_down, high_latency, high_packet_loss
                $table->string('channel'); // email, whatsapp
                $table->text('recipients'); // JSON array
                $table->text('message');
                $table->json('metadata')->nullable(); // Additional context
                $table->boolean('sent_successfully')->default(false);
                $table->text('error_message')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
                
                $table->index(['client_link_id', 'notification_type', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('notification_settings');
    }
};
