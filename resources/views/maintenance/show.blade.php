@extends('layouts.app')

@section('title', 'Detail Pemeliharaan')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tools me-2"></i>Detail Pemeliharaan
            </h1>
            <div>
                @if($maintenance->status == 'pending')
                    <a href="{{ route('maintenance.edit', $maintenance->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                @endif
                <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <!-- Maintenance Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Pemeliharaan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Status:</strong>
                                <p>
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
                                    <span class="badge bg-{{ $statusColors[$maintenance->status] ?? 'secondary' }} fs-6">
                                        {{ $statusLabels[$maintenance->status] ?? $maintenance->status }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <strong>Tipe:</strong>
                                <p>
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
                                    <span class="badge bg-{{ $typeColors[$maintenance->type] ?? 'secondary' }} fs-6">
                                        {{ $typeLabels[$maintenance->type] ?? $maintenance->type }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Tanggal Dijadwalkan:</strong>
                                <p>{{ $maintenance->scheduled_date->format('d F Y') }}</p>
                            </div>
                            @if($maintenance->maintenance_date)
                                <div class="col-md-6">
                                    <strong>Tanggal Pelaksanaan:</strong>
                                    <p>{{ $maintenance->maintenance_date->format('d F Y') }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <strong>Deskripsi:</strong>
                            <p class="text-muted">{{ $maintenance->description }}</p>
                        </div>

                        @if($maintenance->findings)
                            <div class="mb-3">
                                <strong>Temuan:</strong>
                                <p class="text-muted">{{ $maintenance->findings }}</p>
                            </div>
                        @endif

                        @if($maintenance->actions_taken)
                            <div class="mb-3">
                                <strong>Tindakan yang Dilakukan:</strong>
                                <p class="text-muted">{{ $maintenance->actions_taken }}</p>
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Biaya:</strong>
                                <p class="fs-5 text-success">Rp {{ number_format($maintenance->cost ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Diajukan oleh:</strong>
                                <p>{{ $maintenance->requestedBy->name ?? '-' }}</p>
                            </div>
                            @if($maintenance->approvedBy)
                                <div class="col-md-6">
                                    <strong>Disetujui oleh:</strong>
                                    <p>{{ $maintenance->approvedBy->name }}</p>
                                </div>
                            @endif
                        </div>

                        @if($maintenance->document_path)
                            <div class="mb-3">
                                <strong>Dokumen:</strong><br>
                                <a href="{{ asset('storage/' . $maintenance->document_path) }}"
                                    class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="fas fa-file-download me-1"></i>Unduh Dokumen
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Asset Information -->
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-box me-2"></i>Informasi Aset</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Kode Aset:</strong>
                                <p class="text-primary fs-5">{{ $maintenance->asset->asset_code ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Nama Aset:</strong>
                                <p class="fs-5">{{ $maintenance->asset->name ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Kategori:</strong>
                                <p><span class="badge bg-secondary">{{ $maintenance->asset->category->name ?? '-' }}</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <strong>Lokasi:</strong>
                                <p><i
                                        class="fas fa-map-marker-alt text-muted me-1"></i>{{ $maintenance->asset->location->name ?? '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('assets.show', $maintenance->asset->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye me-1"></i>Lihat Detail Aset
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Actions -->
                @if($maintenance->status == 'pending' && auth()->user() && auth()->user()->role && auth()->user()->role->name != 'staff')
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Aksi</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('maintenance.approve', $maintenance->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check me-1"></i>Setujui Pemeliharaan
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                @if($maintenance->status == 'approved')
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-check-double me-2"></i>Selesaikan</h5>
                        </div>
                        <div class="card-body">
                            <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal"
                                data-bs-target="#completeModal">
                                <i class="fas fa-check-double me-1"></i>Selesaikan Pemeliharaan
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Timeline -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <i class="fas fa-circle text-primary"></i>
                                <div>
                                    <strong>Diajukan</strong>
                                    <p class="small text-muted mb-0">{{ $maintenance->created_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>

                            @if($maintenance->status != 'pending')
                                <div class="timeline-item">
                                    <i class="fas fa-circle text-success"></i>
                                    <div>
                                        <strong>Disetujui</strong>
                                        <p class="small text-muted mb-0">{{ $maintenance->updated_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($maintenance->status == 'completed')
                                <div class="timeline-item">
                                    <i class="fas fa-circle text-secondary"></i>
                                    <div>
                                        <strong>Selesai</strong>
                                        <p class="small text-muted mb-0">
                                            {{ $maintenance->maintenance_date?->format('d M Y') ?? '-' }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Modal -->
    @if($maintenance->status == 'approved')
        <div class="modal fade" id="completeModal" tabindex="-1">
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
                                <label class="form-label">Tanggal Pemeliharaan <span class="text-danger">*</span></label>
                                <input type="date" name="maintenance_date" class="form-control" required
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Temuan</label>
                                <textarea name="findings" class="form-control" rows="3"
                                    placeholder="Temuan selama pemeliharaan"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tindakan yang Dilakukan <span class="text-danger">*</span></label>
                                <textarea name="actions_taken" class="form-control" rows="3" required
                                    placeholder="Tindakan yang telah dilakukan"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Biaya <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="cost" class="form-control" min="0" step="0.01" required
                                        value="{{ $maintenance->cost ?? 0 }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Selesaikan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }

        .timeline-item:not(:last-child):before {
            content: '';
            position: absolute;
            left: -24px;
            top: 10px;
            height: calc(100% - 10px);
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item i {
            position: absolute;
            left: -30px;
            top: 2px;
            font-size: 12px;
        }
    </style>
@endsection