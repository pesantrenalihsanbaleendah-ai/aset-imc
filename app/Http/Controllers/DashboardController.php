<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Loan;
use App\Models\Maintenance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $role = $user->role->name ?? null;

        $data = [];

        // Data umum (semua role bisa lihat)
        $data['total_assets'] = Asset::count();
        $data['assets_in_maintenance'] = Asset::where('status', 'maintenance')->count();
        $data['damaged_assets'] = Asset::where('condition', 'poor')->count();
        $data['new_assets'] = Asset::where('created_at', '>=', Carbon::now()->subMonth())->count();

        if ($role === 'super_admin' || $role === 'admin_aset') {
            // Admin dashboard
            $data['total_users'] = User::count();
            $data['total_loans'] = Loan::count();
            $data['pending_loans'] = Loan::where('status', 'pending')->count();
            $data['overdue_loans'] = Loan::where('status', 'pending')
                ->where('expected_return_date', '<', now())
                ->count();
            $data['maintenance_this_month'] = Maintenance::whereBetween('maintenance_date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])->count();
            
            // Chart data
            $data['asset_by_condition'] = Asset::selectRaw('condition, COUNT(*) as count')
                ->groupBy('condition')
                ->get();
            
            $data['asset_by_category'] = Asset::with('category')
                ->selectRaw('category_id, COUNT(*) as count')
                ->groupBy('category_id')
                ->get();

            $data['maintenance_by_month'] = Maintenance::whereYear('maintenance_date', date('Y'))
                ->selectRaw('MONTH(maintenance_date) as month, COUNT(*) as count')
                ->groupBy('month')
                ->get();

            $data['pending_approvals'] = [
                'loans' => Loan::where('status', 'pending')->count(),
                'maintenances' => Maintenance::where('status', 'pending')->count(),
            ];

        } elseif ($role === 'approver') {
            // Manager/Approver dashboard
            $data['pending_loans'] = Loan::where('status', 'pending')->count();
            $data['pending_maintenances'] = Maintenance::where('status', 'pending')->count();
            $data['high_value_assets'] = Asset::where('acquisition_price', '>', 10000000)->count();
            $data['approval_needed'] = [
                'loans' => Loan::where('status', 'pending')->count(),
                'maintenances' => Maintenance::where('status', 'pending')->count(),
            ];

        } elseif ($role === 'staff') {
            // Staff dashboard
            $data['my_assets'] = Asset::where('responsible_user_id', $user->id)->count();
            $data['my_loans'] = Loan::where('user_id', $user->id)->count();
            $data['my_borrowed'] = Loan::where('user_id', $user->id)
                ->where('status', 'approved')
                ->count();
            $data['pending_requests'] = Loan::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count();
            $data['my_asset_list'] = Asset::where('responsible_user_id', $user->id)
                ->with('category', 'location')
                ->get();
        }

        return view('dashboard.index', $data);
    }
}
