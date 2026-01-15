@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-hand-holding me-2"></i>Detail Peminjaman
            </h1>
            <div>
                @if($loan->status == 'pending')
                    <a href="{{ route('loans.edit', $loan->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                @endif
                <a href="{{ route('loans.index') }}" class="btn btn-secondary">
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
                <!-- Loan Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Peminjaman</h5>
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
                                            'rejected' => 'danger',
                                            'returned' => 'secondary'
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Pending',
                                            'approved' => 'Disetujui',
                                            'rejected' => 'Ditolak',
                                            'returned' => 'Dikembalikan'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$loan->status] ?? 'secondary' }} fs-6">
                                        {{ $statusLabels[$loan->status] ?? $loan->status }}
                                    </span>
                                    @if($loan->isOverdue())
                                        <span class="badge bg-danger fs-6 ms-2">Terlambat</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <strong>Pengaju:</strong>
                                <p>{{ $loan->requester_name ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Penanggung Jawab:</strong>
                                <p>{{ $loan->responsible_person ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Tanggal Pinjam:</strong>
                                <p>{{ $loan->loan_date->format('d F Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Tanggal Rencana Kembali:</strong>
                                <p>{{ $loan->expected_return_date->format('d F Y') }}</p>
                            </div>
                        </div>

                        @if($loan->actual_return_date)
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Tanggal Aktual Kembali:</strong>
                                    <p>{{ $loan->actual_return_date->format('d F Y') }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <strong>Tujuan Peminjaman:</strong>
                            <p class="text-muted">{{ $loan->purpose }}</p>
                        </div>

                        @if($loan->notes)
                            <div class="mb-3">
                                <strong>Catatan:</strong>
                                <p class="text-muted">{{ $loan->notes }}</p>
                            </div>
                        @endif

                        @if($loan->approver)
                            <div class="mb-3">
                                <strong>Disetujui/Ditolak oleh:</strong>
                                <p>{{ $loan->approver->name }}</p>
                            </div>
                        @endif

                        @if($loan->document_path)
                            <div class="mb-3">
                                <strong>Dokumen:</strong><br>
                                <a href="{{ asset('storage/' . $loan->document_path) }}" class="btn btn-sm btn-outline-primary"
                                    target="_blank">
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
                                <p class="text-primary fs-5">{{ $loan->asset->asset_code ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Nama Aset:</strong>
                                <p class="fs-5">{{ $loan->asset->name ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Kategori:</strong>
                                <p><span class="badge bg-secondary">{{ $loan->asset->category->name ?? '-' }}</span></p>
                            </div>
                            <div class="col-md-6">
                                <strong>Lokasi:</strong>
                                <p><i
                                        class="fas fa-map-marker-alt text-muted me-1"></i>{{ $loan->asset->location->name ?? '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('assets.show', $loan->asset->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye me-1"></i>Lihat Detail Aset
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Actions -->
                @if($loan->status == 'pending' && auth()->user() && auth()->user()->role && auth()->user()->role->name != 'staff')
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Aksi</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('loans.approve', $loan->id) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check me-1"></i>Setujui Peminjaman
                                </button>
                            </form>
                            <form action="{{ route('loans.reject', $loan->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100"
                                    onclick="return confirm('Yakin ingin menolak peminjaman ini?')">
                                    <i class="fas fa-times me-1"></i>Tolak Peminjaman
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                @if($loan->status == 'approved')
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-undo me-2"></i>Pengembalian</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('loans.return', $loan->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100"
                                    onclick="return confirm('Konfirmasi aset sudah dikembalikan?')">
                                    <i class="fas fa-undo me-1"></i>Kembalikan Aset
                                </button>
                            </form>
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
                                    <p class="small text-muted mb-0">{{ $loan->created_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>

                            @if($loan->status != 'pending')
                                <div class="timeline-item">
                                    <i class="fas fa-circle text-{{ $loan->status == 'approved' ? 'success' : 'danger' }}"></i>
                                    <div>
                                        <strong>{{ $loan->status == 'approved' ? 'Disetujui' : 'Ditolak' }}</strong>
                                        <p class="small text-muted mb-0">{{ $loan->updated_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($loan->actual_return_date)
                                <div class="timeline-item">
                                    <i class="fas fa-circle text-secondary"></i>
                                    <div>
                                        <strong>Dikembalikan</strong>
                                        <p class="small text-muted mb-0">{{ $loan->actual_return_date->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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