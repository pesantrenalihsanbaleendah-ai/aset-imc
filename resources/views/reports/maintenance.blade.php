@extends('layouts.app')

@section('title', 'Laporan Perawatan')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tools me-2"></i>Laporan Perawatan Aset
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Disetujui</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Selesai</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['completed'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Biaya</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($stats['total_cost'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card shadow-sm mb-4 no-print">
            <div class="card-body">
                <form action="{{ route('reports.maintenance') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tgl Dari</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tgl Sampai</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipe Perawatan</label>
                        <select name="type" class="form-select">
                            <option value="">Semua Tipe</option>
                            <option value="preventive" {{ request('type') == 'preventive' ? 'selected' : '' }}>Preventif</option>
                            <option value="corrective" {{ request('type') == 'corrective' ? 'selected' : '' }}>Korektif</option>
                            <option value="predictive" {{ request('type') == 'predictive' ? 'selected' : '' }}>Prediktif</option>
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
                <h6 class="m-0 font-weight-bold text-primary">Data Riwayat Perawatan</h6>
                <span class="badge bg-secondary">Total Biaya: Rp
                    {{ number_format($maintenances->sum('cost'), 0, ',', '.') }}</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th>No</th>
                                <th>Aset</th>
                                <th>Tipe</th>
                                <th>Deskripsi Masalah</th>
                                <th>Vendor/Teknisi</th>
                                <th>Tgl Mulai</th>
                                <th>Tgl Selesai</th>
                                <th>Biaya</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($maintenances as $maintenance)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="font-weight-bold">{{ $maintenance->asset->name }}</div>
                                        <small class="text-primary">{{ $maintenance->asset->code }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $typeLabels = ['routine' => 'Rutin', 'repair' => 'Perbaikan', 'upgrade' => 'Upgrade'];
                                        @endphp
                                        {{ $typeLabels[$maintenance->type] ?? $maintenance->type }}
                                    </td>
                                    <td>{{ Str::limit($maintenance->description, 50) }}</td>
                                    <td>{{ $maintenance->vendor ?? '-' }}</td>
                                    <td>{{ $maintenance->start_date->format('d/m/Y') }}</td>
                                    <td>{{ $maintenance->end_date ? $maintenance->end_date->format('d/m/Y') : '-' }}</td>
                                    <td class="text-end">Rp {{ number_format($maintenance->cost, 0, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $statusClasses = ['pending' => 'warning', 'in_progress' => 'info', 'completed' => 'success', 'cancelled' => 'danger'];
                                            $statusLabels = ['pending' => 'Menunggu', 'in_progress' => 'Proses', 'completed' => 'Selesai', 'cancelled' => 'Batal'];
                                        @endphp
                                        <span class="badge bg-{{ $statusClasses[$maintenance->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$maintenance->status] ?? $maintenance->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">Tidak ada data perawatan ditemukan.</td>
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
                font-size: 10px !important;
            }
        }
    </style>
@endsection