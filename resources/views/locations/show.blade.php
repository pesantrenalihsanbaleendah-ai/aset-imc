@extends('layouts.app')

@section('title', 'Detail Lokasi')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-map-marker-alt me-2"></i>Detail Lokasi
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('locations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
            <a href="{{ route('locations.edit', $location->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Lokasi</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="small text-muted mb-1 d-block">Kode Lokasi</label>
                        <div class="font-weight-bold">{{ $location->code }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1 d-block">Nama Lokasi</label>
                        <div class="font-weight-bold">{{ $location->name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1 d-block">Induk (Parent)</label>
                        <div class="font-weight-bold">
                            @if($location->parent)
                                <a href="{{ route('locations.show', $location->parent->id) }}">
                                    {{ $location->parent->name }} ({{ $location->parent->code }})
                                </a>
                            @else
                                <span class="text-muted">- Utama -</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1 d-block">Lantai / Level</label>
                        <div class="font-weight-bold">{{ $location->level ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1 d-block">Keterangan</label>
                        <div class="font-weight-bold">{{ $location->description ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Aset</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Total Aset di Lokasi Ini</span>
                        <span class="badge bg-primary rounded-pill">{{ $location->assets_count }}</span>
                    </div>
                    @php
                        $totalSubAssets = 0;
                        if($location->children) {
                            foreach($location->children as $child) {
                                $totalSubAssets += $child->assets_count;
                            }
                        }
                    @endphp
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Total Aset di Sub-Lokasi</span>
                        <span class="badge bg-info rounded-pill">{{ $totalSubAssets }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Aset di Lokasi: {{ $location->name }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Kode Aset</th>
                                    <th>Nama Aset</th>
                                    <th>Kategori</th>
                                    <th>Kondisi</th>
                                    <th>Status</th>
                                    <th width="80px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($location->assets as $asset)
                                    <tr>
                                        <td>{{ $asset->code }}</td>
                                        <td>{{ $asset->name }}</td>
                                        <td>{{ $asset->category->name }}</td>
                                        <td>
                                            @php
                                                $conditionClasses = [
                                                    'good' => 'success',
                                                    'damaged' => 'warning',
                                                    'broken' => 'danger'
                                                ];
                                                $conditionLabels = [
                                                    'good' => 'Baik',
                                                    'damaged' => 'Rusak Ringan',
                                                    'broken' => 'Rusak Berat'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $conditionClasses[$asset->condition] ?? 'secondary' }}">
                                                {{ $conditionLabels[$asset->condition] ?? $asset->condition }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusClasses = [
                                                    'available' => 'success',
                                                    'in_use' => 'info',
                                                    'maintenance' => 'warning',
                                                    'disposed' => 'danger'
                                                ];
                                                $statusLabels = [
                                                    'available' => 'Tersedia',
                                                    'in_use' => 'Dipinjam',
                                                    'maintenance' => 'Perawatan',
                                                    'disposed' => 'Dihapus'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusClasses[$asset->status] ?? 'secondary' }}">
                                                {{ $statusLabels[$asset->status] ?? $asset->status }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('assets.show', $asset->id) }}" class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Tidak ada aset di lokasi ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
