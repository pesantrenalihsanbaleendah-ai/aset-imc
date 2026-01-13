@extends('layouts.app')

@section('title', 'Pemeliharaan Aset')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tools me-2"></i>Pemeliharaan Aset
            </h1>
            <a href="{{ route('maintenance.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Ajukan Pemeliharaan
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

        <!-- Filter -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tipe</label>
                        <select name="type" class="form-select">
                            <option value="">Semua Tipe</option>
                            <option value="preventive" {{ request('type') == 'preventive' ? 'selected' : '' }}>Preventif
                            </option>
                            <option value="corrective" {{ request('type') == 'corrective' ? 'selected' : '' }}>Korektif
                            </option>
                            <option value="predictive" {{ request('type') == 'predictive' ? 'selected' : '' }}>Prediktif
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui
                            </option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Aset</th>
                                <th>Tipe</th>
                                <th>Tgl Dijadwalkan</th>
                                <th>Deskripsi</th>
                                <th>Biaya</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($maintenances as $maintenance)
                                <tr>
                                    <td>
                                        <strong>{{ $maintenance->asset->name ?? '-' }}</strong><br>
                                        <small class="text-muted">{{ $maintenance->asset->asset_code ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $typeColors = [
                                                'preventive' => 'info',
                                                'corrective' => 'warning',
                                                'predictive' => 'primary'
                                            ];
                                            $typeLabels = [
                                                'preventive' => 'Preventif',
                                                'corrective' => 'Korektif',
                                                'predictive' => 'Prediktif'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $typeColors[$maintenance->type] ?? 'secondary' }}">
                                            {{ $typeLabels[$maintenance->type] ?? $maintenance->type }}
                                        </span>
                                    </td>
                                    <td>{{ $maintenance->scheduled_date->format('d/m/Y') }}</td>
                                    <td>{{ Str::limit($maintenance->description, 40) }}</td>
                                    <td>Rp {{ number_format($maintenance->cost ?? 0, 0, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'completed' => 'secondary'
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Pending',
                                                'approved' => 'Disetujui',
                                                'completed' => 'Selesai'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$maintenance->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$maintenance->status] ?? $maintenance->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('maintenance.show', $maintenance->id) }}" class="btn btn-info"
                                                title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($maintenance->status == 'pending')
                                                <a href="{{ route('maintenance.edit', $maintenance->id) }}" class="btn btn-warning"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if(auth()->user() && auth()->user()->role && auth()->user()->role->name != 'staff')
                                                    <form action="{{ route('maintenance.approve', $maintenance->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success" title="Setujui">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                            @if($maintenance->status == 'approved')
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#completeModal{{ $maintenance->id }}" title="Selesaikan">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                            @endif>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Complete Modal -->
                                @if($maintenance->status == 'approved')
                                    <div class="modal fade" id="completeModal{{ $maintenance->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Selesaikan Pemeliharaan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('maintenance.complete', $maintenance->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Tanggal Pemeliharaan <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="date" name="maintenance_date" class="form-control"
                                                                required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Temuan</label>
                                                            <textarea name="findings" class="form-control" rows="3"></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Tindakan yang Dilakukan <span
                                                                    class="text-danger">*</span></label>
                                                            <textarea name="actions_taken" class="form-control" rows="3"
                                                                required></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Biaya <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" name="cost" class="form-control" min="0"
                                                                step="0.01" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Selesaikan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada pemeliharaan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Menampilkan {{ $maintenances->firstItem() ?? 0 }} sampai {{ $maintenances->lastItem() ?? 0 }} dari
                        {{ $maintenances->total() }} pemeliharaan
                    </div>
                    {{ $maintenances->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection