@extends('layouts.app')

@section('title', 'Laporan Aset')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-alt me-2"></i>Laporan Daftar Aset
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
        <div class="col-md-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Nilai Perolehan</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($stats['totalAcquisition'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Nilai Buku</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($stats['totalValue'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Akumulasi Penyusutan</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($stats['totalDepreciation'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm mb-4 no-print">
        <div class="card-body">
            <form action="{{ route('reports.asset') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Lokasi</label>
                    <select name="location_id" class="form-select">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="in_use" {{ request('status') == 'in_use' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Perawatan</option>
                        <option value="disposed" {{ request('status') == 'disposed' ? 'selected' : '' }}>Dihapus</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kondisi</label>
                    <select name="condition" class="form-select">
                        <option value="">Semua Kondisi</option>
                        <option value="good" {{ request('condition') == 'good' ? 'selected' : '' }}>Baik</option>
                        <option value="damaged" {{ request('condition') == 'damaged' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="broken" {{ request('condition') == 'broken' ? 'selected' : '' }}>Rusak Berat</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
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
            <h6 class="m-0 font-weight-bold text-primary">Hasil Laporan</h6>
            <span class="badge bg-secondary">Total: {{ $assets->count() }} Item</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th width="120px">Kode Aset</th>
                            <th>Nama Aset</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th width="120px">Tgl Perolehan</th>
                            <th width="150px" class="text-end">Harga Beli</th>
                            <th width="150px" class="text-end">Nilai Buku</th>
                            <th width="100px">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                            <tr>
                                <td class="font-weight-bold text-primary">{{ $asset->code }}</td>
                                <td>{{ $asset->name }}</td>
                                <td>{{ $asset->category->name }}</td>
                                <td>{{ $asset->location->name }}</td>
                                <td>{{ $asset->purchase_date ? $asset->purchase_date->format('d/m/Y') : '-' }}</td>
                                <td class="text-end">Rp {{ number_format($asset->acquisition_price, 0, ',', '.') }}</td>
                                <td class="text-end text-success font-weight-bold">Rp {{ number_format($asset->book_value, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $statusClasses = ['available' => 'success', 'in_use' => 'info', 'maintenance' => 'warning', 'disposed' => 'danger'];
                                        $statusLabels = ['available' => 'Tersedia', 'in_use' => 'Dipinjam', 'maintenance' => 'Perawatan', 'disposed' => 'Dihapus'];
                                    @endphp
                                    <span class="badge bg-{{ $statusClasses[$asset->status] ?? 'secondary' }}">
                                        {{ $statusLabels[$asset->status] ?? $asset->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Tidak ada data aset ditemukan untuk filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($assets->count() > 0)
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="5" class="text-end">TOTAL:</td>
                                <td class="text-end">Rp {{ number_format($stats['totalAcquisition'], 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($stats['totalValue'], 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
    
    <div class="mt-4 text-center d-none d-print-block">
        <p class="small text-muted">Laporan dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        .sidebar { display: none !important; }
        .main-content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
        .topbar { display: none !important; }
        .container-fluid { background: white !important; }
        .card { border: none !important; box-shadow: none !important; }
        .card-header { padding: 0 !important; border: none !important; }
        table { font-size: 10px !important; }
    }
</style>
@endsection
