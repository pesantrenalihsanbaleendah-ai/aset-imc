<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Maintenance::with(['asset', 'requestedBy', 'approvedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $maintenances = $query->latest()->paginate(15);

        return view('maintenance.index', compact('maintenances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $assets = Asset::whereIn('status', ['active', 'damaged'])->get();

        return view('maintenance.create', compact('assets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'type' => 'required|in:preventive,corrective,predictive',
            'scheduled_date' => 'required|date',
            'description' => 'required|string',
            'cost' => 'nullable|numeric|min:0',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $validated['requested_by'] = Auth::id();
        $validated['status'] = 'pending';

        if ($request->hasFile('document')) {
            $validated['document_path'] = $request->file('document')->store('maintenance', 'public');
        }

        Maintenance::create($validated);

        return redirect()->route('maintenance.index')
            ->with('success', 'Pemeliharaan berhasil diajukan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $maintenance = Maintenance::with(['asset.category', 'asset.location', 'requestedBy', 'approvedBy'])
            ->findOrFail($id);

        return view('maintenance.show', compact('maintenance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $maintenance = Maintenance::findOrFail($id);

        // Only allow editing pending maintenances
        if ($maintenance->status !== 'pending') {
            return redirect()->route('maintenance.index')
                ->with('error', 'Hanya pemeliharaan dengan status pending yang dapat diedit.');
        }

        $assets = Asset::whereIn('status', ['active', 'damaged'])->get();

        return view('maintenance.edit', compact('maintenance', 'assets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $maintenance = Maintenance::findOrFail($id);

        if ($maintenance->status !== 'pending') {
            return redirect()->route('maintenance.index')
                ->with('error', 'Hanya pemeliharaan dengan status pending yang dapat diperbarui.');
        }

        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'type' => 'required|in:preventive,corrective,predictive',
            'scheduled_date' => 'required|date',
            'description' => 'required|string',
            'cost' => 'nullable|numeric|min:0',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('document')) {
            if ($maintenance->document_path) {
                \Storage::disk('public')->delete($maintenance->document_path);
            }
            $validated['document_path'] = $request->file('document')->store('maintenance', 'public');
        }

        $maintenance->update($validated);

        return redirect()->route('maintenance.index')
            ->with('success', 'Pemeliharaan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $maintenance = Maintenance::findOrFail($id);

        if ($maintenance->status !== 'pending') {
            return redirect()->route('maintenance.index')
                ->with('error', 'Hanya pemeliharaan dengan status pending yang dapat dihapus.');
        }

        if ($maintenance->document_path) {
            \Storage::disk('public')->delete($maintenance->document_path);
        }

        $maintenance->delete();

        return redirect()->route('maintenance.index')
            ->with('success', 'Pemeliharaan berhasil dihapus.');
    }

    /**
     * Approve maintenance
     */
    public function approve(Request $request, string $id)
    {
        $maintenance = Maintenance::with('asset')->findOrFail($id);

        if ($maintenance->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Pemeliharaan ini sudah diproses.');
        }

        $maintenance->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        // Update asset status to maintenance
        $maintenance->asset->update(['status' => 'maintenance']);

        return redirect()->back()
            ->with('success', 'Pemeliharaan berhasil disetujui.');
    }

    /**
     * Complete maintenance
     */
    public function complete(Request $request, string $id)
    {
        $maintenance = Maintenance::with('asset')->findOrFail($id);

        if ($maintenance->status !== 'approved') {
            return redirect()->back()
                ->with('error', 'Hanya pemeliharaan yang disetujui yang dapat diselesaikan.');
        }

        $validated = $request->validate([
            'maintenance_date' => 'required|date',
            'findings' => 'nullable|string',
            'actions_taken' => 'required|string',
            'cost' => 'required|numeric|min:0',
        ]);

        $validated['status'] = 'completed';

        $maintenance->update($validated);

        // Update asset status back to active
        $maintenance->asset->update(['status' => 'active']);

        return redirect()->back()
            ->with('success', 'Pemeliharaan berhasil diselesaikan.');
    }
}
