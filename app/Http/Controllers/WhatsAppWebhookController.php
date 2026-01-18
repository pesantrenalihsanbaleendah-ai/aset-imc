<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Maintenance;
use App\Models\Setting;
use App\Models\User;
use App\Models\Notification;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
                'approved_at' => now(),
            ]);
            
            // Update asset status to 'on_loan' (dipinjam)
            $target->asset->update(['status' => 'on_loan']);
            
            // Send detailed WhatsApp notification to approver
            $msg = "✅ *PEMINJAMAN DISETUJUI*\n\n";
            $msg .= "📦 Aset: {$target->asset->name}\n";
            $msg .= "🏷️ Kode: {$target->asset->code}\n";
            $msg .= "👤 Peminjam: {$target->requester_name}\n";
            $msg .= "📅 Tanggal Pinjam: " . $target->loan_date->format('d/m/Y') . "\n";
            $msg .= "📅 Tanggal Kembali: " . $target->expected_return_date->format('d/m/Y') . "\n";
            $msg .= "✅ Disetujui oleh: {$user->name}\n";
            $msg .= "\nStatus aset telah diubah menjadi: *DIPINJAM*";
            WhatsAppService::sendMessage($sender, $msg);
            
            // Notify the actual borrower
            if ($target->user->phone) {
                $borrowerMsg = "✅ *PEMINJAMAN DISETUJUI*\n\n";
                $borrowerMsg .= "Halo {$target->user->name},\n\n";
                $borrowerMsg .= "Pengajuan peminjaman Anda telah *DISETUJUI*\n\n";
                $borrowerMsg .= "📦 Aset: {$target->asset->name}\n";
                $borrowerMsg .= "🏷️ Kode: {$target->asset->code}\n";
                $borrowerMsg .= "📅 Tanggal Pinjam: " . $target->loan_date->format('d/m/Y') . "\n";
                $borrowerMsg .= "📅 Harus Kembali: " . $target->expected_return_date->format('d/m/Y') . "\n";
                $borrowerMsg .= "✅ Disetujui oleh: {$user->name}\n\n";
                $borrowerMsg .= "Silakan ambil aset sesuai jadwal. Terima kasih!";
                WhatsAppService::sendMessage($target->user->phone, $borrowerMsg);
            }
            
            // Create dashboard notification
            Notification::create([
                'user_id' => $target->user_id,
                'title' => 'Peminjaman Disetujui',
                'message' => "Peminjaman aset {$target->asset->name} telah disetujui oleh {$user->name}",
                'type' => 'loan_approved',
                'read' => false,
            ]);
            
        } else {
            $target->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            
            // Update asset status to 'maintenance' for maintenance requests
            $target->asset->update(['status' => 'maintenance']);
            
            // Send detailed WhatsApp notification
            $msg = "✅ *PEMELIHARAAN DISETUJUI*\n\n";
            $msg .= "📦 Aset: {$target->asset->name}\n";
            $msg .= "🏷️ Kode: {$target->asset->code}\n";
            $msg .= "🔧 Tipe: {$target->type}\n";
            $msg .= "📅 Tanggal: " . $target->maintenance_date->format('d/m/Y') . "\n";
            $msg .= "✅ Disetujui oleh: {$user->name}\n";
            $msg .= "\nStatus aset telah diubah menjadi: *MAINTENANCE*";
            WhatsAppService::sendMessage($sender, $msg);
            
            // Create dashboard notification
            Notification::create([
                'user_id' => $target->user_id ?? $user->id,
                'title' => 'Pemeliharaan Disetujui',
                'message' => "Pemeliharaan aset {$target->asset->name} telah disetujui oleh {$user->name}",
                'type' => 'maintenance_approved',
                'read' => false,
            ]);
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
                'approved_at' => now(),
            ]);
            
            // Asset status remains unchanged (available/in_use)
            // No need to update asset status on rejection
            
            // Send detailed WhatsApp notification to approver
            $msg = "❌ *PEMINJAMAN DITOLAK*\n\n";
            $msg .= "📦 Aset: {$target->asset->name}\n";
            $msg .= "🏷️ Kode: {$target->asset->code}\n";
            $msg .= "👤 Pengaju: {$target->requester_name}\n";
            $msg .= "📅 Tanggal Pengajuan: " . $target->created_at->format('d/m/Y H:i') . "\n";
            $msg .= "❌ Ditolak oleh: {$user->name}\n";
            $msg .= "\nStatus aset tetap: *{$target->asset->status}*";
            WhatsAppService::sendMessage($sender, $msg);
            
            // Notify the actual borrower
            if ($target->user->phone) {
                $borrowerMsg = "❌ *PEMINJAMAN DITOLAK*\n\n";
                $borrowerMsg .= "Halo {$target->user->name},\n\n";
                $borrowerMsg .= "Mohon maaf, pengajuan peminjaman Anda telah *DITOLAK*\n\n";
                $borrowerMsg .= "📦 Aset: {$target->asset->name}\n";
                $borrowerMsg .= "🏷️ Kode: {$target->asset->code}\n";
                $borrowerMsg .= "📅 Tanggal Pengajuan: " . $target->created_at->format('d/m/Y H:i') . "\n";
                $borrowerMsg .= "❌ Ditolak oleh: {$user->name}\n\n";
                $borrowerMsg .= "Silakan hubungi admin untuk informasi lebih lanjut.";
                WhatsAppService::sendMessage($target->user->phone, $borrowerMsg);
            }
            
            // Create dashboard notification
            Notification::create([
                'user_id' => $target->user_id,
                'title' => 'Peminjaman Ditolak',
                'message' => "Peminjaman aset {$target->asset->name} ditolak oleh {$user->name}",
                'type' => 'loan_rejected',
                'read' => false,
            ]);
            
        } else {
            $target->update([
                'status' => 'rejected',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            
            // Asset status remains unchanged on rejection
            
            // Send detailed WhatsApp notification
            $msg = "❌ *PEMELIHARAAN DITOLAK*\n\n";
            $msg .= "📦 Aset: {$target->asset->name}\n";
            $msg .= "🏷️ Kode: {$target->asset->code}\n";
            $msg .= "🔧 Tipe: {$target->type}\n";
            $msg .= "📅 Tanggal Pengajuan: " . $target->created_at->format('d/m/Y H:i') . "\n";
            $msg .= "❌ Ditolak oleh: {$user->name}\n";
            $msg .= "\nStatus aset tetap: *{$target->asset->status}*";
            WhatsAppService::sendMessage($sender, $msg);
            
            // Create dashboard notification
            Notification::create([
                'user_id' => $target->user_id ?? $user->id,
                'title' => 'Pemeliharaan Ditolak',
                'message' => "Pemeliharaan aset {$target->asset->name} ditolak oleh {$user->name}",
                'type' => 'maintenance_rejected',
                'read' => false,
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}
