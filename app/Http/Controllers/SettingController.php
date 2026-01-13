<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class SettingController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        if (!Auth::user() || !Auth::user()->role || Auth::user()->role->name !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $settings = Setting::all()->groupBy('group');

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        if (!Auth::user() || !Auth::user()->role || Auth::user()->role->name !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($request->settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if ($setting) {
                // Handle file uploads for image type
                if ($setting->type === 'image' && $request->hasFile("settings.{$key}")) {
                    // Delete old file if exists
                    if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                        Storage::disk('public')->delete($setting->value);
                    }

                    // Store new file
                    $value = $request->file("settings.{$key}")->store('settings', 'public');
                }

                // Handle boolean values
                if ($setting->type === 'boolean') {
                    $value = $request->has("settings.{$key}") ? '1' : '0';
                }

                $setting->update(['value' => $value]);
            }
        }

        // Clear cache
        Setting::clearCache();

        return redirect()->back()
            ->with('success', 'Pengaturan berhasil diperbarui.');
    }

    /**
     * Test WhatsApp connection
     */
    public function testWhatsApp()
    {
        if (!Auth::user() || !Auth::user()->role || Auth::user()->role->name !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $endpoint = Setting::get('whatsapp_endpoint');
        $apiKey = Setting::get('whatsapp_api_key');
        $sender = Setting::get('whatsapp_sender_number');
        $receiver = Setting::get('whatsapp_receiver_number');

        if (!$endpoint || !$apiKey || !$sender || !$receiver) {
            return redirect()->back()
                ->with('error', 'Lengkapi semua pengaturan WhatsApp terlebih dahulu.');
        }

        // Implement actual WhatsApp API call using WhatsAppService
        $success = \App\Services\WhatsAppService::sendMessage($receiver, 'Ini adalah pesan test dari Sistem Manajemen Aset IMC. Jika Anda menerima ini, integrasi WhatsApp berhasil!');

        if ($success) {
            return redirect()->back()
                ->with('success', 'Pesan test berhasil dikirim ke ' . $receiver . '!');
        }

        return redirect()->back()
            ->with('error', 'Gagal mengirim pesan test. Pastikan pengaturan benar (API Key, Sender, Receiver) dan cek log sistem.');
    }

    /**
     * Reset settings to default
     */
    public function reset(Request $request)
    {
        if (!Auth::user() || !Auth::user()->role || Auth::user()->role->name !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $group = $request->input('group');

        if ($group) {
            $settings = Setting::query()->where('group', $group)->get();

            foreach ($settings as $setting) {
                // Delete uploaded files
                if ($setting->type === 'image' && $setting->value) {
                    if (Storage::disk('public')->exists($setting->value)) {
                        Storage::disk('public')->delete($setting->value);
                    }
                }
            }

            Setting::where('group', $group)->delete();

            // Re-run migration defaults for this group
            // This is a simplified version - you might want to handle this better

            Setting::clearCache();

            return redirect()->back()
                ->with('success', "Pengaturan {$group} berhasil direset.");
        }

        return redirect()->back()
            ->with('error', 'Grup pengaturan tidak valid.');
    }
}
