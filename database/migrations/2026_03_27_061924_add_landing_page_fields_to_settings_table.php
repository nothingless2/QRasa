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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('hero_bg')->nullable();
            $table->string('about_title')->nullable()->default('Kesan Pertama yang Menggoda');
            $table->text('about_text')->nullable();
            $table->string('about_image')->nullable();
            $table->string('contact_whatsapp')->nullable();
            $table->string('contact_instagram')->nullable();
            $table->text('map_iframe')->nullable();
            $table->json('operational_hours')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_bg', 'about_title', 'about_text', 'about_image', 
                'contact_whatsapp', 'contact_instagram', 'map_iframe', 'operational_hours'
            ]);
        });
    }
};
