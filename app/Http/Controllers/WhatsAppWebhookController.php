<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Maintenance;
use App\Models\Setting;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Handle incoming messages from WhatsApp Gateway (Mutekar / Fonnte)
     */
    public function handle(Request $request)
    {
        // Log incoming request for debugging
        Log::info('WhatsApp Webhook Received:', $request->all());

        // For Mutekar, usually the message is in 'message' or 'text'
        // For Fonnte, it's often in 'message' or 'text'
        $message = $request->input('message') ?? $request->input('text');
        $sender = $request->input('sender') ?? $request->input('from');

        if (!$message || !$sender) {
            return response()->json(['status' => 'error', 'message' => 'Invalid data'], 400);
        }

        $trimmedMessage = trim($message);

        // Check if the message is "1" (approve) or "2" (reject)
        if ($trimmedMessage === '1') {
            return $this->processApproval($sender);
        } elseif ($trimmedMessage === '2') {
            return $this->processRejection($sender);
        }

        return response()->json(['status' => 'ignored']);
    }

    /**
     * Process approval based on sender
     */
    private function processApproval($sender)
    {
        // 1. Identify user by phone number
        // Normalize sender number (remove @g.us, ensure format)
        $cleanSender = preg_replace('/[^0-9]/', '', $sender);
        
        $user = User::where('phone', 'like', "%{$cleanSender}%")->first();
        
        // If not found by phone, check if the sender matches configured receiver (group/admin)
        $configuredReceiver = Setting::get('whatsapp_receiver_number');
        $isConfiguredReceiver = (strpos($sender, $configuredReceiver) !== false || strpos($configuredReceiver, $cleanSender) !== false);

        // If it's a group message and the group ID matches our receiver number
        if (!$user && $isConfiguredReceiver) {
            // Treat as system admin/approver if message comes from configured group/number
            // We search for the first Super Admin or Approver to act as system context
            $user = User::whereHas('role', function($q) {
                $q->whereIn('name', ['super_admin', 'admin_aset', 'approver']);
            })->first();
        }

        if (!$user) {
            Log::warning("WhatsApp Approval: User not found for sender {$sender}");
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        // 2. Find the latest pending Loan or Maintenance
        $loan = Loan::where('status', 'pending')->latest()->first();
        $maintenance = Maintenance::where('status', 'pending')->latest()->first();

        // Determine which one is more recent if both exist
        $target = null;
        if ($loan && $maintenance) {
            $target = ($loan->created_at > $maintenance->created_at) ? $loan : $maintenance;
        } else {
            $target = $loan ?: $maintenance;
        }

        if (!$target) {
            WhatsAppService::sendMessage($sender, "Tidak ada pengajuan (Peminjaman/Pemeliharaan) yang sedang menunggu persetujuan.");
            return response()->json(['status' => 'no_pending_requests']);
        }

        // 3. Perform approval
        if ($target instanceof Loan) {
            $target->update([
                'status' => 'approved',
                'approver_id' => $user->id,
            ]);
            $target->asset->update(['status' => 'maintenance']);
            
            $msg = "✅ *Peminjaman DISETUJUI*\n\nAset: {$target->asset->name}\nPeminjam: {$target->user->name}\nDisetujui oleh: {$user->name}";
            WhatsAppService::sendMessage($sender, $msg);
            
            // Notify the actual borrower
            if ($target->user->phone) {
                WhatsAppService::sendMessage($target->user->phone, "Halo {$target->user->name}, pengajuan peminjaman aset *{$target->asset->name}* Anda telah *DISETUJUI*.");
            }
        } else {
            $target->update([
                'status' => 'approved',
                'approved_by' => $user->id,
            ]);
            $target->asset->update(['status' => 'maintenance']);
            
            $msg = "✅ *Pemeliharaan DISETUJUI*\n\nAset: {$target->asset->name}\nTipe: {$target->type}\nDisetujui oleh: {$user->name}";
            WhatsAppService::sendMessage($sender, $msg);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Process rejection based on sender
     */
    private function processRejection($sender)
    {
        // 1. Identify user by phone number
        $cleanSender = preg_replace('/[^0-9]/', '', $sender);
        
        $user = User::where('phone', 'like', "%{$cleanSender}%")->first();
        
        // If not found by phone, check if the sender matches configured receiver (group/admin)
        $configuredReceiver = Setting::get('whatsapp_receiver_number');
        $isConfiguredReceiver = (strpos($sender, $configuredReceiver) !== false || strpos($configuredReceiver, $cleanSender) !== false);

        // If it's a group message and the group ID matches our receiver number
        if (!$user && $isConfiguredReceiver) {
            $user = User::whereHas('role', function($q) {
                $q->whereIn('name', ['super_admin', 'admin_aset', 'approver']);
            })->first();
        }

        if (!$user) {
            Log::warning("WhatsApp Rejection: User not found for sender {$sender}");
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        // 2. Find the latest pending Loan or Maintenance
        $loan = Loan::where('status', 'pending')->latest()->first();
        $maintenance = Maintenance::where('status', 'pending')->latest()->first();

        // Determine which one is more recent if both exist
        $target = null;
        if ($loan && $maintenance) {
            $target = ($loan->created_at > $maintenance->created_at) ? $loan : $maintenance;
        } else {
            $target = $loan ?: $maintenance;
        }

        if (!$target) {
            WhatsAppService::sendMessage($sender, "Tidak ada pengajuan (Peminjaman/Pemeliharaan) yang sedang menunggu persetujuan.");
            return response()->json(['status' => 'no_pending_requests']);
        }

        // 3. Perform rejection
        if ($target instanceof Loan) {
            $target->update([
                'status' => 'rejected',
                'approver_id' => $user->id,
            ]);
            
            $msg = "❌ *Peminjaman DITOLAK*\n\nAset: {$target->asset->name}\nPengaju: {$target->requester_name}\nDitolak oleh: {$user->name}";
            WhatsAppService::sendMessage($sender, $msg);
            
            // Notify the actual borrower
            if ($target->user->phone) {
                WhatsAppService::sendMessage($target->user->phone, "Halo {$target->user->name}, mohon maaf pengajuan peminjaman aset *{$target->asset->name}* telah *DITOLAK*.");
            }
        } else {
            $target->update([
                'status' => 'rejected',
                'approved_by' => $user->id,
            ]);
            
            $msg = "❌ *Pemeliharaan DITOLAK*\n\nAset: {$target->asset->name}\nTipe: {$target->type}\nDitolak oleh: {$user->name}";
            WhatsAppService::sendMessage($sender, $msg);
        }

        return response()->json(['status' => 'success']);
    }
}
