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
        if (Schema::hasTable('deliverables')) {
            return;
        }

        Schema::create('deliverables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feasibility_id')->constrained('feasibilities')->onDelete('cascade');
            
            // Basic delivery information
            $table->string('status')->default('Open'); // Open, InProgress, Delivery
            $table->string('delivery_id')->unique()->nullable(); // Auto-generated ID
            
            // Site Information (from your sheet)
            $table->text('site_address')->nullable();
            $table->string('local_contact')->nullable();
            $table->string('state')->nullable();
            $table->string('gst_number')->nullable();
            $table->string('link_type')->nullable();
            $table->string('speed_in_mbps')->nullable();
            $table->integer('no_of_links')->nullable();
            
            // Vendor Information
            $table->string('vendor')->nullable();
            $table->string('circuit_id')->nullable();
            $table->string('plans_name')->nullable();
            $table->string('speed_in_mbps_plan')->nullable();
            $table->integer('no_of_months_renewal')->nullable();
            $table->date('date_of_activation')->nullable();
            $table->string('sla')->nullable();
            
            // Mode of Delivery
            $table->string('mode_of_delivery')->nullable(); // DHCP / Static IP / PPPoE
            
            // PPPoE Fields
            $table->string('pppoe_username')->nullable();
            $table->string('pppoe_password')->nullable();
            
            // DHCP Fields (if DHCP means dynamic)
            
            // Static IP Fields
            $table->string('static_ip_address')->nullable();
            $table->string('static_vlan')->nullable();
            $table->string('static_subnet_mask')->nullable();
            $table->string('static_gateway')->nullable();
            $table->string('static_vlan_tag')->nullable();
            
            // Link Status
            $table->string('status_of_link')->default('Pending'); // Active, Inactive, Pending
            
            // Additional Charges
            $table->decimal('otc_extra_charges', 10, 2)->nullable();
            
            // Upload OTC Bill
            $table->string('otc_bill_file')->nullable(); // File path for uploaded bill
            
            // Tracking
            $table->timestamp('delivered_at')->nullable();
            $table->string('delivered_by')->nullable();
            $table->text('delivery_notes')->nullable();
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('status');
            $table->index('feasibility_id');
            $table->index('delivery_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliverables');
    }
};
