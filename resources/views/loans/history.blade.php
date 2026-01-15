@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-history me-2"></i>Riwayat Peminjaman
            </h1>
            <a href="{{ route('loans.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('loans.history') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
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

        <!-- History Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Role Login</th>
                                <th>Pengaju</th>
                                <th>Penanggung Jawab</th>
                                <th>Aset</th>
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
                                        @if($loan->user && $loan->user->role)
                                            <span class="badge bg-info">{{ $loan->user->role->description }}</span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $loan->requester_name ?? '-' }}</strong>
                                        @if($loan->user)
                                            <br><small class="text-muted">Login: {{ $loan->user->name }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $loan->responsible_person ?? '-' }}</td>
                                    <td>
                                        <strong>{{ $loan->asset->name ?? '-' }}</strong>
                                        <br><small class="text-muted">{{ $loan->asset->asset_code ?? '-' }}</small>
                                    </td>
                                    <td>{{ $loan->loan_date ? $loan->loan_date->format('d M Y') : '-' }}</td>
                                    <td>
                                        @if($loan->actual_return_date)
                                            <span class="text-success">{{ $loan->actual_return_date->format('d M Y') }}</span>
                                        @else
                                            <span class="text-muted">{{ $loan->expected_return_date ? $loan->expected_return_date->format('d M Y') : '-' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'returned' => 'info'
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Menunggu',
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
                                        <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada riwayat peminjaman.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Menampilkan {{ $loans->firstItem() ?? 0 }} sampai {{ $loans->lastItem() ?? 0 }} dari {{ $loans->total() }} riwayat
                    </div>
                    {{ $loans->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
