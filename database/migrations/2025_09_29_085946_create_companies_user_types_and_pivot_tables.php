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
         // 1️⃣ Companies Table
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('cin_llpin')->nullable();
            $table->string('contact_no')->nullable(); // Landline/Phone combined
            $table->string('phone_no')->nullable();
            $table->string('email_1')->nullable();
            $table->string('email_2')->nullable();
            $table->text('address')->nullable();
            $table->string('billing_logo')->nullable();
            $table->string('billing_sign_normal')->nullable();
            $table->string('billing_sign_digital')->nullable();
            $table->string('gst_no')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('tan_number')->nullable();
             $table->string('color')->default('#333333');
        $table->string('logo')->nullable();
       
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });

        // 2️⃣ User Types Table (renamed to user_types ✅)
        // Schema::create('user_types', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->string('description');
        //     $table->enum('status', ['Active', 'Inactive'])->default('Active');
        //     $table->timestamps();
        // });

        // 3️⃣ Pivot Table: company_user (Many-to-Many)
        Schema::create('company_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_user');
        // Schema::dropIfExists('user_types');
        Schema::dropIfExists('companies');
    }
};
