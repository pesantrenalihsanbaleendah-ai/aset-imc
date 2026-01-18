<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        // Check if user is super admin
        if (!Auth::user() || !Auth::user()->role || Auth::user()->role->name !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $query = User::with('role');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $users = $query->latest()->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        if (!Auth::user() || !Auth::user()->role || Auth::user()->role->name !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        if (!Auth::user() || !Auth::user()->role || Auth::user()->role->name !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'employee_id' => 'nullable|string|max:50|unique:users,employee_id',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'is_active' => 'nullable|in:0,1',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->input('is_active', '0') === '1';

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Show the form for editing user
     */
    public function edit(string $id)
    {
        if (!Auth::user() || !Auth::user()->role || Auth::user()->role->name !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, string $id)
    {
        if (!Auth::user() || !Auth::user()->role || Auth::user()->role->name !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'employee_id' => 'nullable|string|max:50|unique:users,employee_id,' . $id,
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'is_active' => 'nullable|in:0,1',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->input('is_active', '0') === '1';

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(string $id)
    {
        if (!Auth::user() || !Auth::user()->role || Auth::user()->role->name !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($id);

        // Prevent deleting own account
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Check if user has related data
        if ($user->assets()->count() > 0 || $user->loans()->count() > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User tidak dapat dihapus karena memiliki data terkait.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(string $id)
    {
        if (!Auth::user() || !Auth::user()->role || Auth::user()->role->name !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($id);

        // Prevent deactivating own account
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "User berhasil {$status}.");
    }
}
