<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\WhatsAppService;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Loan::with(['asset', 'user', 'user.role', 'approver']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by user (for staff)
        $user = Auth::user();
        if ($user && $user->role && $user->role->name === 'staff') {
            $query->where('user_id', $user->id);
        }

        $loans = $query->latest()->paginate(15);

        return view('loans.index', compact('loans'));
    }

    /**
     * Display loan history
     */
    public function history(Request $request)
    {
        $query = Loan::with(['asset', 'user', 'user.role', 'approver']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $loans = $query->latest()->paginate(15);

        return view('loans.history', compact('loans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $assets = Asset::where('status', 'active')->get();
        $users = User::all();

        return view('loans.create', compact('assets', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_ids' => 'required|array|min:1',
            'asset_ids.*' => 'required|exists:assets,id',
            'requester_name' => 'required|string|max:255',
            'responsible_person' => 'required|string|max:255',
            'purpose' => 'required|string',
            'loan_date' => 'required|date',
            'expected_return_date' => 'required|date|after:loan_date',
            'notes' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $documentPath = null;

        // Handle document upload using PHP native
        if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['document']['tmp_name'];
            $originalName = $_FILES['document']['name'];
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            
            $destinationPath = storage_path('app/public/loans');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $fullPath = $destinationPath . DIRECTORY_SEPARATOR . $filename;
            if (move_uploaded_file($tmpName, $fullPath)) {
                $documentPath = 'loans/' . $filename;
            }
        }

        $createdLoans = [];
        $assetIds = $validated['asset_ids'];

        // Create a loan for each selected asset
        foreach ($assetIds as $assetId) {
            $loanData = [
                'asset_id' => $assetId,
                'user_id' => auth()->id(),
                'requester_name' => $validated['requester_name'],
                'responsible_person' => $validated['responsible_person'],
                'purpose' => $validated['purpose'],
                'loan_date' => $validated['loan_date'],
                'expected_return_date' => $validated['expected_return_date'],
                'notes' => $validated['notes'] ?? null,
                'document_path' => $documentPath,
                'status' => 'pending',
            ];

            $loan = Loan::create($loanData);
            $createdLoans[] = $loan->load(['asset', 'user']);
        }

        // Notify Admin with approval instructions
        $adminPhone = \App\Models\Setting::get('whatsapp_receiver_number');
        if ($adminPhone && count($createdLoans) > 0) {
            $firstLoan = $createdLoans[0];
            
            // Build asset list
            $assetList = '';
            foreach ($createdLoans as $index => $loan) {
                $assetList .= ($index + 1) . ". {$loan->asset->name} ({$loan->asset->asset_code})\n";
            }
            
            $msg = "*Pengajuan Peminjaman Baru*\n\n"
                . "Pengaju: {$firstLoan->requester_name}\n"
                . "Penanggung Jawab: {$firstLoan->responsible_person}\n"
                . "Jumlah Aset: " . count($createdLoans) . "\n\n"
                . "Aset yang Dipinjam:\n{$assetList}\n"
                . "Tgl Pinjam: " . $firstLoan->loan_date->format('d M Y') . "\n"
                . "Tgl Kembali: " . $firstLoan->expected_return_date->format('d M Y') . "\n"
                . "Tujuan: {$firstLoan->purpose}\n\n"
                . "━━━━━━━━━━━━━━━━━━\n"
                . "💡 *Cara Approval:*\n"
                . "• Ketik *1* untuk SETUJU\n"
                . "• Ketik *2* untuk TOLAK\n"
                . "━━━━━━━━━━━━━━━━━━";
            WhatsAppService::sendMessage($adminPhone, $msg);
        }

        $assetCount = count($createdLoans);
        $message = $assetCount > 1 
            ? "Peminjaman untuk {$assetCount} aset berhasil diajukan." 
            : 'Peminjaman berhasil diajukan.';

        return redirect()->route('loans.index')
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $loan = Loan::with(['asset.category', 'asset.location', 'user', 'approver'])
            ->findOrFail($id);

        return view('loans.show', compact('loan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $loan = Loan::findOrFail($id);

        // Only allow editing pending loans
        if ($loan->status !== 'pending') {
            return redirect()->route('loans.index')
                ->with('error', 'Hanya peminjaman dengan status pending yang dapat diedit.');
        }

        $assets = Asset::where('status', 'active')->get();
        $users = User::all();

        return view('loans.edit', compact('loan', 'assets', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $loan = Loan::findOrFail($id);

        if ($loan->status !== 'pending') {
            return redirect()->route('loans.index')
                ->with('error', 'Hanya peminjaman dengan status pending yang dapat diperbarui.');
        }

        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'user_id' => 'required|exists:users,id',
            'responsible_person' => 'required|string|max:255',
            'purpose' => 'required|string',
            'loan_date' => 'required|date',
            'expected_return_date' => 'required|date|after:loan_date',
            'notes' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        // Handle document upload using PHP native
        if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['document']['tmp_name'];
            $originalName = $_FILES['document']['name'];
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            
            $destinationPath = storage_path('app/public/loans');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $fullPath = $destinationPath . DIRECTORY_SEPARATOR . $filename;
            if (move_uploaded_file($tmpName, $fullPath)) {
                // Delete old document if exists
                if ($loan->document_path) {
                    $oldFile = storage_path('app/public/' . $loan->document_path);
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
                $validated['document_path'] = 'loans/' . $filename;
            }
        }

        $loan->update($validated);

        return redirect()->route('loans.index')
            ->with('success', 'Peminjaman berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $loan = Loan::findOrFail($id);

        if ($loan->status !== 'pending') {
            return redirect()->route('loans.index')
                ->with('error', 'Hanya peminjaman dengan status pending yang dapat dihapus.');
        }

        if ($loan->document_path) {
            \Storage::disk('public')->delete($loan->document_path);
        }

        $loan->delete();

        return redirect()->route('loans.index')
            ->with('success', 'Peminjaman berhasil dihapus.');
    }

    /**
     * Approve loan
     */
    public function approve(string $id)
    {
        $loan = Loan::with('asset')->findOrFail($id);

        if ($loan->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Peminjaman ini sudah diproses.');
        }

        $loan->update([
            'status' => 'approved',
            'approver_id' => Auth::id(),
        ]);

        // Update asset status
        $loan->asset->update(['status' => 'maintenance']);

        // Notify User
        if ($loan->user->phone) {
            $msg = "*Update Peminjaman Aset*\n\n"
                . "Halo {$loan->user->name}, pengajuan peminjaman aset *{$loan->asset->name}* Anda telah *DISETUJUI*.\n"
                . "Silakan ambil aset di lokasi terkait.\n\n"
                . "Terima kasih.";
            WhatsAppService::sendMessage($loan->user->phone, $msg);
        }

        return redirect()->back()
            ->with('success', 'Peminjaman berhasil disetujui.');
    }

    /**
     * Reject loan
     */
    public function reject(string $id)
    {
        $loan = Loan::findOrFail($id);

        if ($loan->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Peminjaman ini sudah diproses.');
        }

        $loan->update([
            'status' => 'rejected',
            'approver_id' => Auth::id(),
        ]);

        // Notify User
        if ($loan->user && $loan->user->phone) {
            $loan = $loan->load('asset'); // ensure asset is loaded
            $msg = "*Update Peminjaman Aset*\n\n"
                . "Halo {$loan->user->name}, mohon maaf pengajuan peminjaman aset *{$loan->asset->name}* Anda telah *DITOLAK*.\n\n"
                . "Terima kasih.";
            WhatsAppService::sendMessage($loan->user->phone, $msg);
        }

        return redirect()->back()
            ->with('success', 'Peminjaman berhasil ditolak.');
    }

    /**
     * Return asset
     */
    public function return(Request $request, string $id)
    {
        $loan = Loan::with('asset')->findOrFail($id);

        if ($loan->status !== 'approved') {
            return redirect()->back()
                ->with('error', 'Hanya peminjaman yang disetujui yang dapat dikembalikan.');
        }

        $loan->update([
            'status' => 'returned',
            'actual_return_date' => now(),
        ]);

        // Update asset status back to active
        $loan->asset->update(['status' => 'active']);

        return redirect()->back()
            ->with('success', 'Aset berhasil dikembalikan.');
    }

    /**
     * Print loan document
     */
    public function print(string $id)
    {
        $loan = Loan::with(['asset.category', 'asset.location', 'user', 'approver'])
            ->findOrFail($id);

        return view('loans.print', compact('loan'));
    }
}
