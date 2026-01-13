@extends('layouts.app')

@section('title', 'Lokasi')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-map-marker-alt me-2"></i>Manajemen Lokasi
            </h1>
            <a href="{{ route('locations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Tambah Lokasi
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

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Lokasi</th>
                                <th>Parent</th>
                                <th>Level</th>
                                <th>Gedung</th>
                                <th>Lantai</th>
                                <th>Ruangan</th>
                                <th>Jumlah Aset</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($locations as $location)
                                <tr>
                                    <td><strong class="text-primary">{{ $location->name }}</strong></td>
                                    <td>{{ $location->parent->name ?? '-' }}</td>
                                    <td>
                                        @php
                                            $levels = [
                                                'building' => 'Gedung',
                                                'floor' => 'Lantai',
                                                'room' => 'Ruangan',
                                                'other' => 'Lainnya'
                                            ];
                                        @endphp
                                        <span class="badge bg-info">{{ $levels[$location->level] ?? $location->level }}</span>
                                    </td>
                                    <td>{{ $location->building ?? '-' }}</td>
                                    <td>{{ $location->floor ?? '-' }}</td>
                                    <td>{{ $location->room ?? '-' }}</td>
                                    <td><span class="badge bg-secondary">{{ $location->assets_count }} aset</span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('locations.show', $location->id) }}" class="btn btn-info"
                                                title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('locations.edit', $location->id) }}" class="btn btn-warning"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger"
                                                onclick="confirmDelete({{ $location->id }})" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <form id="delete-form-{{ $location->id }}"
                                            action="{{ route('locations.destroy', $location->id) }}" method="POST"
                                            class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada lokasi. Mulai dengan menambahkan lokasi baru.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Menampilkan {{ $locations->firstItem() ?? 0 }} sampai {{ $locations->lastItem() ?? 0 }} dari
                        {{ $locations->total() }} lokasi
                    </div>
                    {{ $locations->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm('Apakah Anda yakin ingin menghapus lokasi ini? Tindakan ini tidak dapat dibatalkan.')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
@endsection