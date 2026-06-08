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
        Schema::table('pesans', function (Blueprint $table) {
            $table->integer('subtotal')->default(0)->after('meja_id');
            $table->integer('service_charge')->default(0)->after('subtotal');
            $table->integer('pajak_pb1')->default(0)->after('service_charge');
            // 'total' column already exists and will serve as Grand Total
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesans', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'service_charge', 'pajak_pb1']);
        });
    }
};
