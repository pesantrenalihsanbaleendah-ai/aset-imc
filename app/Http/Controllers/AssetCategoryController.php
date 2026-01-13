<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = AssetCategory::withCount('assets')->latest()->paginate(15);
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:asset_categories',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'depreciation_method' => 'nullable|in:straight_line,declining_balance,units_of_production',
            'depreciation_years' => 'nullable|integer|min:1|max:50',
        ]);

        AssetCategory::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = AssetCategory::withCount('assets')->findOrFail($id);
        $assets = $category->assets()->with(['location', 'responsibleUser'])->paginate(10);

        return view('categories.show', compact('category', 'assets'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = AssetCategory::findOrFail($id);
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = AssetCategory::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|unique:asset_categories,code,' . $id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'depreciation_method' => 'nullable|in:straight_line,declining_balance,units_of_production',
            'depreciation_years' => 'nullable|integer|min:1|max:50',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = AssetCategory::findOrFail($id);

        // Check if category has assets
        if ($category->assets()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki aset.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
