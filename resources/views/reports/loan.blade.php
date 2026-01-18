@extends('layouts.app')

@section('title', 'Laporan Peminjaman')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-hand-holding me-2"></i>Laporan Peminjaman Aset
            </h1>
            <div class="d-flex gap-2">
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
                <button onclick="window.print()" class="btn btn-primary d-none d-sm-inline-block">
                    <i class="fas fa-print me-1"></i>Cetak Laporan
                </button>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row mb-4 no-print">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Dipinjam</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Kembali</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['returned'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Terlambat (Overdue)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['overdue'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card shadow-sm mb-4 no-print">
            <div class="card-body">
                <form action="{{ route('reports.loan') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tgl Dari</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tgl Sampai</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Kembali</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-info w-100">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Report Table -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Peminjaman</h6>
                <span class="badge bg-secondary">Total: {{ $loans->count() }} Record</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th>No</th>
                                <th>Peminjam</th>
                                <th>Aset</th>
                                <th>Tgl Pinjam</th>
                                <th>Estimasi Kembali</th>
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="font-weight-bold">{{ $loan->user->name }}</div>
                                        <small class="text-muted">{{ $loan->user->employee_id ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">{{ $loan->asset->name }}</div>
                                        <small class="text-primary">{{ $loan->asset->code }}</small>
                                    </td>
                                    <td>{{ $loan->loan_date ? $loan->loan_date->format('d/m/Y') : '-' }}</td>
                                    <td>{{ $loan->expected_return_date ? $loan->expected_return_date->format('d/m/Y') : '-' }}</td>
                                    <td>{{ $loan->actual_return_date ? $loan->actual_return_date->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'pending' => 'warning',
                                                'approved' => 'info',
                                                'rejected' => 'danger',
                                                'returned' => 'success'
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Menunggu',
                                                'approved' => 'Dipinjam',
                                                'rejected' => 'Ditolak',
                                                'returned' => 'Kembali'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusClasses[$loan->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$loan->status] ?? $loan->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Tidak ada data peminjaman ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .sidebar {
                display: none !important;
            }

            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 0 !important;
            }

            .topbar {
                display: none !important;
            }

            table {
                font-size: 11px !important;
            }
        }
    </style>
@endsection