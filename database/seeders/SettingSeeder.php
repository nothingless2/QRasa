<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Setting::create([
            'store_name' => 'QRasa Cafe & Resto',
            'store_address' => "Jl. Malioboro No. 100, Yogyakarta\nTelp: (0274) 123456",
            'receipt_footer' => 'Terima kasih atas kunjungan Anda.',
            'logo_path' => null, // null means use default text or generic logo
            'tax_percent' => 11,
            'service_percent' => 5,
            'welcome_title' => 'Sistem Pemesanan Cerdas QRasa',
            'welcome_subtitle' => 'Pesan dari meja Anda, scan kode QR, dan nikmati kemudahan bertransaksi secara digital.',
            'welcome_footer' => 'Sistem Manajemen QRasa &copy; ' . date('Y'),
        ]);
    }
}
