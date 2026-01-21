@extends('layouts.app')

@section('title', 'Manajemen Aset')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-boxes me-2"></i>Manajemen Aset
            </h1>
            <div>
                <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-import me-1"></i>Impor
                </button>
                <a href="{{ route('assets.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Tambah Aset Baru
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('assets.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Cari</label>
                        <input type="text" name="search" class="form-control" placeholder="Kode aset, nama, serial..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
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
                    <div class="col-md-2">
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
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>
                                Pemeliharaan</option>
                            <option value="damaged" {{ request('status') == 'damaged' ? 'selected' : '' }}>Rusak</option>
                            <option value="disposed" {{ request('status') == 'disposed' ? 'selected' : '' }}>Dibuang</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kondisi</label>
                        <select name="condition" class="form-select">
                            <option value="">Semua Kondisi</option>
                            <option value="good" {{ request('condition') == 'good' ? 'selected' : '' }}>Baik</option>
                            <option value="acceptable" {{ request('condition') == 'acceptable' ? 'selected' : '' }}>Cukup
                            </option>
                            <option value="poor" {{ request('condition') == 'poor' ? 'selected' : '' }}>Buruk</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Assets Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Kode Aset</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                                <th>Kondisi</th>
                                <th>Nilai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assets as $asset)
                                <tr>
                                    <td>
                                        <strong class="text-primary">{{ $asset->asset_code }}</strong>
                                        @if($asset->qr_code)
                                            <i class="fas fa-qrcode text-success ms-1" title="QR Code Dibuat"></i>
                                        @endif
                                    </td>
                                    <td>{{ $asset->name }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $asset->category->name ?? '-' }}</span>
                                    </td>
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
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('assets.show', $asset->id) }}" class="btn btn-info" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-warning"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('assets.qr', $asset->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success" title="Buat QR">
                                                    <i class="fas fa-qrcode"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-danger"
                                                onclick="confirmDelete({{ $asset->id }})" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <form id="delete-form-{{ $asset->id }}"
                                            action="{{ route('assets.destroy', $asset->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Tidak ada aset ditemukan. Mulai dengan menambahkan aset baru.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Menampilkan {{ $assets->firstItem() ?? 0 }} sampai {{ $assets->lastItem() ?? 0 }} dari
                        {{ $assets->total() }} aset
                    </div>
                    {{ $assets->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Impor Aset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('assets.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih File</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                            <small class="text-muted">Format yang didukung: Excel (.xlsx, .xls) atau CSV</small>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Unduh file template untuk memastikan format yang benar.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Impor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm('Apakah Anda yakin ingin menghapus aset ini? Tindakan ini tidak dapat dibatalkan.')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
@endsection