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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            
            // Branding Toko Asli
            $table->string('store_name')->default('QRasa Cafe & Resto');
            $table->text('store_address')->nullable();
            $table->string('receipt_footer')->default('Terima kasih atas kunjungan Anda');
            $table->string('logo_path')->nullable();
            
            // Keuangan & Pajak
            $table->integer('tax_percent')->default(11);
            $table->integer('service_percent')->default(5);
            
            // Front-End Pemasaran (Landing Page)
            $table->string('welcome_title')->default('Sistem Pemesanan Cerdas QRasa');
            $table->text('welcome_subtitle')->nullable();
            $table->string('welcome_footer')->default('Sistem Manajemen QRasa &copy; 2026');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
