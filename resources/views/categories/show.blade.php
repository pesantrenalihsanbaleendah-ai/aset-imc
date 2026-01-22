@extends('layouts.app')

@section('title', 'Detail Kategori')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tag me-2"></i>Detail Kategori
            </h1>
            <div>
                <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <!-- Category Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Kategori</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Kode:</strong>
                            <p class="text-primary fs-5">{{ $category->code }}</p>
                        </div>

                        <div class="mb-3">
                            <strong>Nama Kategori:</strong>
                            <p class="fs-5">{{ $category->name }}</p>
                        </div>

                        @if($category->description)
                            <div class="mb-3">
                                <strong>Deskripsi:</strong>
                                <p class="text-muted">{{ $category->description }}</p>
                            </div>
                        @endif

                        <hr>

                        <div class="mb-2">
                            <strong>Metode Depresiasi:</strong>
                            <p>
                                @if($category->depreciation_method)
                                    @php
                                        $methods = [
                                            'straight_line' => 'Garis Lurus',
                                            'declining_balance' => 'Saldo Menurun',
                                            'units_of_production' => 'Unit Produksi'
                                        ];
                                    @endphp
                                    <span class="badge bg-info">{{ $methods[$category->depreciation_method] }}</span>
                                @else
                                    <span class="text-muted">Tidak diatur</span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-2">
                            <strong>Masa Manfaat:</strong>
                            <p>{{ $category->depreciation_years ? $category->depreciation_years . ' tahun' : 'Tidak diatur' }}
                            </p>
                        </div>

                        <hr>

                        <div class="mb-2">
                            <strong>Dibuat:</strong>
                            <p>{{ $category->created_at->format('d M Y H:i') }}</p>
                        </div>

                        <div class="mb-0">
                            <strong>Terakhir Diperbarui:</strong>
                            <p>{{ $category->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Statistik</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total Aset:</span>
                            <strong class="fs-4 text-primary">{{ $category->assets_count }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Aset Aktif:</span>
                            <strong>{{ $category->assets->where('status', 'active')->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Dalam Pemeliharaan:</span>
                            <strong>{{ $category->assets->where('status', 'maintenance')->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Rusak:</span>
                            <strong>{{ $category->assets->where('status', 'damaged')->count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <!-- Assets in this Category -->
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Aset dalam Kategori Ini</h5>
                    </div>
                    <div class="card-body">
                        @if($assets->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Kode Aset</th>
                                            <th>Nama</th>
                                            <th>Lokasi</th>
                                            <th>Status</th>
                                            <th>Kondisi</th>
                                            <th>Nilai Buku</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assets as $asset)
                                            <tr>
                                                <td><strong class="text-primary">{{ $asset->asset_code }}</strong></td>
                                                <td>{{ $asset->name }}</td>
                                                <td>
                                                    <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                                    {{ $asset->location->name ?? '-' }}
                                                </td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'active' => 'success',
                                                            'maintenance' => 'warning',
                                                            'damaged' => 'danger',
                                                            'disposed' => 'secondary'
                                                        ];
                                                        $statusLabels = [
                                                            'active' => 'Aktif',
                                                            'maintenance' => 'Pemeliharaan',
                                                            'damaged' => 'Rusak',
                                                            'disposed' => 'Dibuang'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColors[$asset->status] ?? 'secondary' }}">
                                                        {{ $statusLabels[$asset->status] ?? $asset->status }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $conditionColors = [
                                                            'good' => 'success',
                                                            'acceptable' => 'warning',
                                                            'poor' => 'danger'
                                                        ];
                                                        $conditionLabels = [
                                                            'good' => 'Baik',
                                                            'acceptable' => 'Cukup',
                                                            'poor' => 'Buruk'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $conditionColors[$asset->condition] ?? 'secondary' }}">
                                                        {{ $conditionLabels[$asset->condition] ?? $asset->condition }}
                                                    </span>
                                                </td>
                                                <td>Rp {{ number_format($asset->book_value, 0, ',', '.') }}</td>
                                                <td>
                                                    <a href="{{ route('assets.show', $asset->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2">`n                    <div class="text-muted small">
                                    Menampilkan {{ $assets->firstItem() ?? 0 }} sampai {{ $assets->lastItem() ?? 0 }} dari
                                    {{ $assets->total() }} aset
                                </div>
                                {{ $assets->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada aset dalam kategori ini.</p>
                                <a href="{{ route('assets.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Tambah Aset
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection