@extends('layouts.app')

@section('title', 'Kategori Aset')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tags me-2"></i>Kategori Aset
            </h1>
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Tambah Kategori
            </a>
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

        <!-- Categories Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Metode Depresiasi</th>
                                <th>Tahun Depresiasi</th>
                                <th>Jumlah Aset</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td><strong class="text-primary">{{ $category->code }}</strong></td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ Str::limit($category->description ?? '-', 50) }}</td>
                                    <td>
                                        @if($category->depreciation_method)
                                            @php
                                                $methods = [
                                                    'straight_line' => 'Garis Lurus',
                                                    'declining_balance' => 'Saldo Menurun',
                                                    'units_of_production' => 'Unit Produksi'
                                                ];
                                            @endphp
                                            <span class="badge bg-info">{{ $methods[$category->depreciation_method] ?? '-' }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $category->depreciation_years ? $category->depreciation_years . ' tahun' : '-' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $category->assets_count }} aset</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('categories.show', $category->id) }}" class="btn btn-info"
                                                title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger"
                                                onclick="confirmDelete({{ $category->id }})" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <form id="delete-form-{{ $category->id }}"
                                            action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                            class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada kategori. Mulai dengan menambahkan kategori baru.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2">
                    <div class="text-muted small">
                        Menampilkan {{ $categories->firstItem() ?? 0 }} sampai {{ $categories->lastItem() ?? 0 }} dari
                        {{ $categories->total() }} kategori
                    </div>
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm('Apakah Anda yakin ingin menghapus kategori ini? Tindakan ini tidak dapat dibatalkan.')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
@endsection