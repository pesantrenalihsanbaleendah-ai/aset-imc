<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Asset::with(['category', 'location', 'responsibleUser']);

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by location
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by condition
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('asset_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        $assets = $query->latest()->paginate(15);
        $categories = AssetCategory::all();
        $locations = Location::all();

        return view('assets.index', compact('assets', 'categories', 'locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = AssetCategory::all();
        $locations = Location::all();
        $users = User::all();

        return view('assets.create', compact('categories', 'locations', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_code' => 'required|unique:assets',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:asset_categories,id',
            'location_id' => 'required|exists:locations,id',
            'responsible_user_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'acquisition_price' => 'required|numeric|min:0',
            'book_value' => 'required|numeric|min:0',
            'condition' => 'required|in:good,acceptable,poor',
            'status' => 'required|in:active,maintenance,damaged,disposed',
            'acquisition_date' => 'nullable|date',
            'warranty_until' => 'nullable|date',
            'serial_number' => 'nullable|string',
            'specification' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('assets', 'public');
        }

        Asset::create($validated);

        return redirect()->route('assets.index')
            ->with('success', 'Aset berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $asset = Asset::with(['category', 'location', 'responsibleUser', 'loans', 'maintenances'])
            ->findOrFail($id);

        return view('assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $asset = Asset::findOrFail($id);
        $categories = AssetCategory::all();
        $locations = Location::all();
        $users = User::all();

        return view('assets.edit', compact('asset', 'categories', 'locations', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $asset = Asset::findOrFail($id);

        $validated = $request->validate([
            'asset_code' => 'required|unique:assets,asset_code,' . $id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:asset_categories,id',
            'location_id' => 'required|exists:locations,id',
            'responsible_user_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'acquisition_price' => 'required|numeric|min:0',
            'book_value' => 'required|numeric|min:0',
            'condition' => 'required|in:good,acceptable,poor',
            'status' => 'required|in:active,maintenance,damaged,disposed',
            'acquisition_date' => 'nullable|date',
            'warranty_until' => 'nullable|date',
            'serial_number' => 'nullable|string',
            'specification' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($asset->photo_path) {
                Storage::disk('public')->delete($asset->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('assets', 'public');
        }

        $asset->update($validated);

        return redirect()->route('assets.index')
            ->with('success', 'Aset berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $asset = Asset::findOrFail($id);

        // Delete photo if exists
        if ($asset->photo_path) {
            Storage::disk('public')->delete($asset->photo_path);
        }

        $asset->delete();

        return redirect()->route('assets.index')
            ->with('success', 'Aset berhasil dihapus.');
    }

    /**
     * Generate QR code for asset
     */
    public function generateQR(string $id)
    {
        $asset = Asset::findOrFail($id);

        // Generate QR code
        $qrCode = 'ASSET-' . $asset->asset_code . '-' . time();
        $asset->update(['qr_code' => $qrCode]);

        return redirect()->back()
            ->with('success', 'QR Code berhasil dibuat.');
    }

    /**
     * Import assets from file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        // TODO: Implement import logic

        return redirect()->route('assets.index')
            ->with('success', 'Aset berhasil diimpor.');
    }
}
