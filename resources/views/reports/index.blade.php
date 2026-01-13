@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-bar me-2"></i>Laporan & Analisis
            </h1>
        </div>

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-left-primary">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Aset</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['total_assets']) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-boxes fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-left-success">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Nilai Total Aset
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp
                                    {{ number_format($stats['total_asset_value'], 0, ',', '.') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-left-info">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Peminjaman</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['total_loans']) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hand-holding fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-left-warning">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Pemeliharaan
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['total_maintenances']) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-tools fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Types -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Laporan Aset</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Laporan lengkap mengenai aset yang dimiliki, termasuk nilai, kondisi, dan
                            lokasi.</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Daftar aset berdasarkan kategori</li>
                            <li><i class="fas fa-check text-success me-2"></i>Nilai total dan depresiasi</li>
                            <li><i class="fas fa-check text-success me-2"></i>Status dan kondisi aset</li>
                            <li><i class="fas fa-check text-success me-2"></i>Distribusi lokasi</li>
                        </ul>
                        <a href="{{ route('reports.asset') }}" class="btn btn-primary">
                            <i class="fas fa-file-alt me-1"></i>Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-hand-holding me-2"></i>Laporan Peminjaman</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Laporan peminjaman aset, termasuk status, peminjam, dan keterlambatan.</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Riwayat peminjaman</li>
                            <li><i class="fas fa-check text-success me-2"></i>Status persetujuan</li>
                            <li><i class="fas fa-check text-success me-2"></i>Peminjaman terlambat</li>
                            <li><i class="fas fa-check text-success me-2"></i>Analisis peminjam</li>
                        </ul>
                        <a href="{{ route('reports.loan') }}" class="btn btn-info">
                            <i class="fas fa-file-alt me-1"></i>Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Laporan Pemeliharaan</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Laporan pemeliharaan aset, termasuk biaya, tipe, dan frekuensi.</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Riwayat pemeliharaan</li>
                            <li><i class="fas fa-check text-success me-2"></i>Total biaya pemeliharaan</li>
                            <li><i class="fas fa-check text-success me-2"></i>Tipe pemeliharaan</li>
                            <li><i class="fas fa-check text-success me-2"></i>Jadwal mendatang</li>
                        </ul>
                        <a href="{{ route('reports.maintenance') }}" class="btn btn-warning">
                            <i class="fas fa-file-alt me-1"></i>Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Laporan Depresiasi</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Laporan depresiasi aset untuk analisis nilai dan perencanaan keuangan.</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Nilai akuisisi vs nilai buku</li>
                            <li><i class="fas fa-check text-success me-2"></i>Persentase depresiasi</li>
                            <li><i class="fas fa-check text-success me-2"></i>Depresiasi per kategori</li>
                            <li><i class="fas fa-check text-success me-2"></i>Proyeksi nilai aset</li>
                        </ul>
                        <a href="{{ route('reports.depreciation') }}" class="btn btn-success">
                            <i class="fas fa-file-alt me-1"></i>Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .border-left-primary {
                border-left: 4px solid #4e73df;
            }

            .border-left-success {
                border-left: 4px solid #1cc88a;
            }

            .border-left-info {
                border-left: 4px solid #36b9cc;
            }

            .border-left-warning {
                border-left: 4px solid #f6c23e;
            }
        </style>
    </div>
@endsection