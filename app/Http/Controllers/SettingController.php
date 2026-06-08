<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        if (!$setting) {
            // Fallback safety
            $setting = Setting::create([
                'store_name' => 'QRasa Cafe & Resto',
                'receipt_footer' => 'Terima kasih atas kunjungan Anda.',
                'tax_percent' => 11,
                'service_percent' => 5,
                'welcome_title' => 'Sistem Pemesanan Cerdas QRasa',
                'welcome_footer' => 'Sistem Manajemen QRasa &copy; ' . date('Y'),
            ]);
        }
        return view('admin.settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'nullable|string',
            'receipt_footer' => 'required|string|max:255',
            'tax_percent' => 'required|integer|min:0|max:100',
            'service_percent' => 'required|integer|min:0|max:100',
            'welcome_title' => 'required|string|max:255',
            'welcome_subtitle' => 'nullable|string',
            'welcome_footer' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'google_analytics_id' => 'nullable|string|max:255',
            'facebook_pixel_id' => 'nullable|string|max:255',
            'hero_bg' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'about_title' => 'nullable|string|max:255',
            'about_text' => 'nullable|string',
            'about_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'featured_menu_count' => 'nullable|integer|min:1|max:12',
            'contact_whatsapp' => 'nullable|string|max:50',
            'contact_instagram' => 'nullable|string|max:50',
            'map_iframe' => 'nullable|string',
            'operational_hours' => 'nullable|array',
            'operational_hours.*.day' => 'nullable|string',
            'operational_hours.*.time' => 'nullable|string',
        ]);

        $setting = Setting::first();

        $data = $request->except(['_token', '_method', 'logo', 'hero_bg', 'about_image']);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path)) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            // Store new logo
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo_path'] = $path;
        }

        if ($request->hasFile('hero_bg')) {
            if ($setting->hero_bg && Storage::disk('public')->exists($setting->hero_bg)) {
                Storage::disk('public')->delete($setting->hero_bg);
            }
            $data['hero_bg'] = $request->file('hero_bg')->store('settings', 'public');
        }

        if ($request->hasFile('about_image')) {
            if ($setting->about_image && Storage::disk('public')->exists($setting->about_image)) {
                Storage::disk('public')->delete($setting->about_image);
            }
            $data['about_image'] = $request->file('about_image')->store('settings', 'public');
        }

        $setting->update($data);

        return redirect()->back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}
