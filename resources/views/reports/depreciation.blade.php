@extends('layouts.app')

@section('title', 'Laporan Penyusutan Aset')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-line me-2"></i>Laporan Penyusutan Aset
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
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Nilai Perolehan
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp
                                    {{ number_format($stats['totalAcquisition'], 0, ',', '.') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Nilai Buku
                                    (Saat Ini)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp
                                    {{ number_format($stats['totalBookValue'], 0, ',', '.') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-book fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Akumulasi
                                    Penyusutan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp
                                    {{ number_format($stats['totalDepreciation'], 0, ',', '.') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Rata-rata Laju
                                    Penyusutan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['averageDepreciationRate'], 2) }}%</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-percentage fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card shadow-sm mb-4 no-print">
            <div class="card-body">
                <form action="{{ route('reports.depreciation') }}" method="GET" class="row g-3">
                    <div class="col-md-9">
                        <label class="form-label">Filter Kategori</label>
                        <select name="category_id" class="form-select">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
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
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th>No</th>
                                <th>Aset</th>
                                <th>Tgl Perolehan</th>
                                <th class="text-end">Harga Perolehan</th>
                                <th class="text-end">Nilai Buku</th>
                                <th class="text-end">Penyusutan</th>
                                <th class="text-center">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($depreciationData as $data)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="font-weight-bold">{{ $data['asset']->name }}</div>
                                        <small class="text-primary">{{ $data['asset']->code }}</small>
                                    </td>
                                    <td>{{ $data['asset']->purchase_date ? $data['asset']->purchase_date->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="text-end">Rp {{ number_format($data['asset']->acquisition_price, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end text-success font-weight-bold">Rp
                                        {{ number_format($data['asset']->book_value, 0, ',', '.') }}</td>
                                    <td class="text-end text-danger">Rp {{ number_format($data['depreciation'], 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-danger" role="progressbar"
                                                style="width: {{ $data['depreciation_rate'] }}%"
                                                aria-valuenow="{{ $data['depreciation_rate'] }}" aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                        <small>{{ number_format($data['depreciation_rate'], 1) }}%</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Tidak ada data aset ditemukan.</td>
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

            .card {
                border: none !important;
            }

            table {
                font-size: 10px !important;
            }
        }

        .border-left-primary {
            border-left: .25rem solid #4e73df !important;
        }

        .border-left-success {
            border-left: .25rem solid #1cc88a !important;
        }

        .border-left-info {
            border-left: .25rem solid #36b9cc !important;
        }

        .border-left-warning {
            border-left: .25rem solid #f6c23e !important;
        }

        .border-left-danger {
            border-left: .25rem solid #e74a3b !important;
        }
    </style>
@endsection