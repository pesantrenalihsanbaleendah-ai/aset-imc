<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Loan;
use App\Models\Maintenance;
use App\Models\AssetCategory;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display report dashboard
     */
    public function index()
    {
        $stats = [
            'total_assets' => Asset::count(),
            'total_loans' => Loan::count(),
            'total_maintenances' => Maintenance::count(),
            'total_asset_value' => Asset::sum('book_value'),
        ];

        return view('reports.index', compact('stats'));
    }

    /**
     * Asset report
     */
    public function assetReport(Request $request)
    {
        $query = Asset::with(['category', 'location', 'responsibleUser']);

        // Filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        $assets = $query->get();
        $categories = AssetCategory::all();
        $locations = Location::all();

        // Statistics
        $totalValue = $assets->sum('book_value');
        $totalAcquisition = $assets->sum('acquisition_price');
        $totalDepreciation = $totalAcquisition - $totalValue;

        $stats = compact('totalValue', 'totalAcquisition', 'totalDepreciation');

        return view('reports.asset', compact('assets', 'categories', 'locations', 'stats'));
    }

    /**
     * Loan report
     */
    public function loanReport(Request $request)
    {
        $query = Loan::with(['asset', 'user', 'approver']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('loan_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('loan_date', '<=', $request->date_to);
        }

        $loans = $query->get();

        // Statistics
        $stats = [
            'total' => $loans->count(),
            'pending' => $loans->where('status', 'pending')->count(),
            'approved' => $loans->where('status', 'approved')->count(),
            'rejected' => $loans->where('status', 'rejected')->count(),
            'returned' => $loans->where('status', 'returned')->count(),
            'overdue' => $loans->filter(fn($loan) => $loan->isOverdue())->count(),
        ];

        return view('reports.loan', compact('loans', 'stats'));
    }

    /**
     * Maintenance report
     */
    public function maintenanceReport(Request $request)
    {
        $query = Maintenance::with(['asset', 'requestedBy', 'approvedBy']);

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('scheduled_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('scheduled_date', '<=', $request->date_to);
        }

        $maintenances = $query->get();

        // Statistics
        $stats = [
            'total' => $maintenances->count(),
            'pending' => $maintenances->where('status', 'pending')->count(),
            'approved' => $maintenances->where('status', 'approved')->count(),
            'completed' => $maintenances->where('status', 'completed')->count(),
            'total_cost' => $maintenances->sum('cost'),
            'preventive' => $maintenances->where('type', 'preventive')->count(),
            'corrective' => $maintenances->where('type', 'corrective')->count(),
            'predictive' => $maintenances->where('type', 'predictive')->count(),
        ];

        return view('reports.maintenance', compact('maintenances', 'stats'));
    }

    /**
     * Depreciation report
     */
    public function depreciationReport(Request $request)
    {
        $query = Asset::with(['category']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $assets = $query->get();
        $categories = AssetCategory::all();

        // Calculate depreciation
        $depreciationData = $assets->map(function ($asset) {
            $depreciation = $asset->acquisition_price - $asset->book_value;
            $depreciationRate = $asset->acquisition_price > 0
                ? ($depreciation / $asset->acquisition_price) * 100
                : 0;

            return [
                'asset' => $asset,
                'depreciation' => $depreciation,
                'depreciation_rate' => $depreciationRate,
            ];
        });

        $totalAcquisition = $assets->sum('acquisition_price');
        $totalBookValue = $assets->sum('book_value');
        $totalDepreciation = $totalAcquisition - $totalBookValue;
        $averageDepreciationRate = $totalAcquisition > 0
            ? ($totalDepreciation / $totalAcquisition) * 100
            : 0;

        $stats = compact('totalAcquisition', 'totalBookValue', 'totalDepreciation', 'averageDepreciationRate');

        return view('reports.depreciation', compact('depreciationData', 'categories', 'stats'));
    }

    /**
     * Export report to CSV
     */
    public function export(Request $request)
    {
        $type = $request->input('type', 'asset');
        $filename = "laporan_{$type}_" . now()->format('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        switch ($type) {
            case 'asset':
                $columns = ['Kode', 'Nama', 'Kategori', 'Lokasi', 'Tgl Perolehan', 'Harga Perolehan', 'Nilai Buku', 'Status', 'Kondisi'];
                $data = Asset::with(['category', 'location'])->get();
                $callback = function () use ($data, $columns) {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, $columns);
                    foreach ($data as $item) {
                        fputcsv($file, [
                            $item->code,
                            $item->name,
                            $item->category->name,
                            $item->location->name,
                            $item->purchase_date ? $item->purchase_date->format('d-m-Y') : '-',
                            $item->acquisition_price,
                            $item->book_value,
                            $item->status,
                            $item->condition
                        ]);
                    }
                    fclose($file);
                };
                break;

            case 'loan':
                $columns = ['ID', 'Peminjam', 'Aset', 'Tgl Pinjam', 'Tgl Kembali', 'Status'];
                $data = Loan::with(['user', 'asset'])->get();
                $callback = function () use ($data, $columns) {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, $columns);
                    foreach ($data as $item) {
                        fputcsv($file, [
                            $item->id,
                            $item->user->name,
                            $item->asset->name . " ({$item->asset->code})",
                            $item->loan_date ? $item->loan_date->format('d-m-Y') : '-',
                            $item->return_date ? $item->return_date->format('d-m-Y') : '-',
                            $item->status
                        ]);
                    }
                    fclose($file);
                };
                break;

            case 'maintenance':
                $columns = ['ID', 'Aset', 'Tipe', 'Deskripsi', 'Vendor', 'Tgl Mulai', 'Tgl Selesai', 'Biaya', 'Status'];
                $data = Maintenance::with(['asset'])->get();
                $callback = function () use ($data, $columns) {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, $columns);
                    foreach ($data as $item) {
                        fputcsv($file, [
                            $item->id,
                            $item->asset->name . " ({$item->asset->code})",
                            $item->type,
                            $item->description,
                            $item->vendor,
                            $item->start_date ? $item->start_date->format('d-m-Y') : '-',
                            $item->end_date ? $item->end_date->format('d-m-Y') : '-',
                            $item->cost,
                            $item->status
                        ]);
                    }
                    fclose($file);
                };
                break;

            default:
                return redirect()->back()->with('error', 'Tipe laporan tidak valid.');
        }

        return response()->stream($callback, 200, $headers);
    }
}
