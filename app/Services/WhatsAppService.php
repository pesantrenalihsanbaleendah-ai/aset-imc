<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message using the configured gateway
     *
     * @param string $number Recipient number
     * @param string $message Message content
     * @return bool
     */
    public static function sendMessage($number, $message)
    {
        $enabled = Setting::get('whatsapp_enabled', '0');
        if ($enabled !== '1') {
            return false;
        }

        $endpoint = Setting::get('whatsapp_endpoint');
        $apiKey = Setting::get('whatsapp_api_key');
        $sender = Setting::get('whatsapp_sender_number');

        if (!$endpoint || !$apiKey || !$sender) {
            Log::warning('WhatsApp Gateway settings are incomplete.');
            return false;
        }

        try {
            // Check if endpoint is mutekar.com based on user request
            if (strpos($endpoint, 'mutekar.com') !== false) {
                $response = Http::asJson()->post($endpoint, [
                    'api_key' => $apiKey,
                    'sender' => $sender,
                    'number' => $number,
                    'message' => $message,
                    'footer' => Setting::get('site_name', 'System Aset')
                ]);
            } elseif (strpos($endpoint, 'fonnte.com') !== false) {
                // Fonnte support
                $response = Http::withHeaders([
                    'Authorization' => $apiKey,
                ])->asForm()->post($endpoint, [
                            'target' => $number,
                            'message' => $message,
                        ]);
            } else {
                // Generic POST
                $response = Http::post($endpoint, [
                    'api_key' => $apiKey,
                    'sender' => $sender,
                    'number' => $number,
                    'message' => $message,
                ]);
            }

            if ($response->successful()) {
                return true;
            }

            Log::error('WhatsApp API Error: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('WhatsApp Connection Error: ' . $e->getMessage());
            return false;
        }
    }
}
