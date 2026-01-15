@extends('layouts.app')

@section('title', 'Peminjaman Aset')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-hand-holding me-2"></i>Peminjaman Aset
            </h1>
            <a href="{{ route('loans.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Ajukan Peminjaman
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
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui
                            </option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan
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
                                <th>Peminjam</th>
                                <th>Penanggung Jawab</th>
                                <th>Tujuan</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                                <tr>
                                    <td>
                                        <strong>{{ $loan->asset->name ?? '-' }}</strong><br>
                                        <small class="text-muted">{{ $loan->asset->asset_code ?? '-' }}</small>
                                    </td>
                                    <td>{{ $loan->user->name ?? '-' }}</td>
                                    <td>{{ $loan->responsible_person ?? '-' }}</td>
                                    <td>{{ Str::limit($loan->purpose, 30) }}</td>
                                    <td>{{ $loan->loan_date->format('d/m/Y') }}</td>
                                    <td>
                                        {{ $loan->expected_return_date->format('d/m/Y') }}
                                        @if($loan->isOverdue())
                                            <span class="badge bg-danger ms-1">Terlambat</span>
                                        @endif
                                    </td>
                                    <td>
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
                                        <span class="badge bg-{{ $statusColors[$loan->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$loan->status] ?? $loan->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-info" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($loan->status == 'pending')
                                                <a href="{{ route('loans.edit', $loan->id) }}" class="btn btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if(auth()->user() && auth()->user()->role && auth()->user()->role->name != 'staff')
                                                    <form action="{{ route('loans.approve', $loan->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success" title="Setujui">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('loans.reject', $loan->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger" title="Tolak">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                            @if($loan->status == 'approved')
                                                <form action="{{ route('loans.return', $loan->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary" title="Kembalikan">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada peminjaman.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Menampilkan {{ $loans->firstItem() ?? 0 }} sampai {{ $loans->lastItem() ?? 0 }} dari
                        {{ $loans->total() }} peminjaman
                    </div>
                    {{ $loans->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection