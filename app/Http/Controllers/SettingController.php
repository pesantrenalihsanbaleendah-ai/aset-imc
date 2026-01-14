<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        // Debug: Log what we're receiving
        Log::info('POST Data:', $request->all());
        Log::info('FILES Data:', $_FILES);

        // Get all settings that might be updated
        $allSettings = Setting::all();

        foreach ($allSettings as $setting) {
            $key = $setting->key;
            
            // Handle file uploads for image type - SOLUSI FINAL (PHP NATIVE)
            if ($setting->type === 'image') {
                // Cek langsung dari $_FILES (bypass Laravel request)
                if (isset($_FILES['settings']['name'][$key]) && 
                    !empty($_FILES['settings']['name'][$key]) && 
                    $_FILES['settings']['error'][$key] === UPLOAD_ERR_OK) {
                    
                    $tmpName = $_FILES['settings']['tmp_name'][$key];
                    $originalName = $_FILES['settings']['name'][$key];
                    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                    
                    // Generate unique filename
                    $filename = uniqid() . '.' . $extension;
                    
                    // Tentukan path tujuan
                    $destinationPath = storage_path('app/public/settings');
                    
                    // Buat folder jika belum ada
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    
                    $fullPath = $destinationPath . DIRECTORY_SEPARATOR . $filename;
                    
                    // Move file menggunakan PHP native
                    if (move_uploaded_file($tmpName, $fullPath)) {
                        // Delete old file if exists
                        if ($setting->value) {
                            $oldFile = storage_path('app/public/' . $setting->value);
                            if (file_exists($oldFile)) {
                                unlink($oldFile);
                            }
                        }
                        
                        // Save relative path to database
                        $relativePath = 'settings/' . $filename;
                        $setting->update(['value' => $relativePath]);
                        Log::info("Successfully uploaded {$key}: {$relativePath}");
                    } else {
                        Log::error("Failed to move uploaded file for {$key}");
                    }
                }
                // If no file uploaded, don't update (keep existing value)
                continue;
            }

            // Handle boolean values
            if ($setting->type === 'boolean') {
                $value = $request->has("settings.{$key}") ? '1' : '0';
                $setting->update(['value' => $value]);
                continue;
            }

            // Handle other types (text, textarea, color, etc.)
            if ($request->has("settings.{$key}")) {
                $value = $request->input("settings.{$key}");
                // Skip if value is an UploadedFile object (already handled above)
                if ($value instanceof \Illuminate\Http\UploadedFile) {
                    continue;
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
                if ($setting->type === 'image') {
                    $oldPath = $setting->value;
                    if (!empty($oldPath) && is_string($oldPath)) {
                        $trimmedPath = trim($oldPath);
                        if ($trimmedPath !== '' && Storage::disk('public')->exists($trimmedPath)) {
                            Storage::disk('public')->delete($trimmedPath);
                        }
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
