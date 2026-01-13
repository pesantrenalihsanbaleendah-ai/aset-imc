<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::withCount('assets')
            ->with('parent')
            ->latest()
            ->paginate(15);

        return view('locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentLocations = Location::whereNull('parent_id')->get();
        return view('locations.create', compact('parentLocations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:locations,id',
            'level' => 'required|in:building,floor,room,other',
            'address' => 'nullable|string',
            'building' => 'nullable|string|max:100',
            'floor' => 'nullable|string|max:50',
            'room' => 'nullable|string|max:50',
        ]);

        Location::create($validated);

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $location = Location::withCount('assets', 'children')->findOrFail($id);
        $assets = $location->assets()->with(['category', 'responsibleUser'])->paginate(10);

        return view('locations.show', compact('location', 'assets'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $location = Location::findOrFail($id);
        $parentLocations = Location::whereNull('parent_id')
            ->where('id', '!=', $id)
            ->get();

        return view('locations.edit', compact('location', 'parentLocations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $location = Location::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:locations,id',
            'level' => 'required|in:building,floor,room,other',
            'address' => 'nullable|string',
            'building' => 'nullable|string|max:100',
            'floor' => 'nullable|string|max:50',
            'room' => 'nullable|string|max:50',
        ]);

        $location->update($validated);

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $location = Location::findOrFail($id);

        // Check if location has assets
        if ($location->assets()->count() > 0) {
            return redirect()->route('locations.index')
                ->with('error', 'Lokasi tidak dapat dihapus karena masih memiliki aset.');
        }

        // Check if location has children
        if ($location->children()->count() > 0) {
            return redirect()->route('locations.index')
                ->with('error', 'Lokasi tidak dapat dihapus karena masih memiliki sub-lokasi.');
        }

        $location->delete();

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi berhasil dihapus.');
    }
}
