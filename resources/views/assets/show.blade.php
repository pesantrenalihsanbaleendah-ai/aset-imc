@extends('layouts.app')

@section('title', 'Detail Aset')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-box me-2"></i>Detail Aset
            </h1>
            <div>
                <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
                <a href="{{ route('assets.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Asset Information -->
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Aset</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Kode Aset:</strong>
                                <p class="text-primary fs-5">{{ $asset->asset_code }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Nama Aset:</strong>
                                <p class="fs-5">{{ $asset->name }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Kategori:</strong>
                                <p><span class="badge bg-secondary">{{ $asset->category->name ?? '-' }}</span></p>
                            </div>
                            <div class="col-md-6">
                                <strong>Lokasi:</strong>
                                <p><i class="fas fa-map-marker-alt text-muted me-1"></i>{{ $asset->location->name ?? '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Status:</strong>
                                <p>
                                    @php
                                        $statusColors = [
                                            'active' => 'success',
                                            'maintenance' => 'warning',
                                            'damaged' => 'danger',
                                            'disposed' => 'secondary'
                                        ];
                                        $statusLabels = [
                                            'active' => 'Aktif',
                                            'maintenance' => 'Perawatan',
                                            'damaged' => 'Rusak',
                                            'disposed' => 'Dibuang'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$asset->status] ?? 'secondary' }}">
                                        {{ $statusLabels[$asset->status] ?? $asset->status }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <strong>Kondisi:</strong>
                                <p>
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
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Penanggung Jawab:</strong>
                                <p>{{ $asset->responsibleUser->name ?? 'Belum Ditugaskan' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Nomor Seri:</strong>
                                <p>{{ $asset->serial_number ?? '-' }}</p>
                            </div>
                        </div>

                        @if($asset->description)
                            <div class="mb-3">
                                <strong>Deskripsi:</strong>
                                <p class="text-muted">{{ $asset->description }}</p>
                            </div>
                        @endif

                        @if($asset->specification)
                            <div class="mb-3">
                                <strong>Spesifikasi:</strong>
                                <p class="text-muted">{{ $asset->specification }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Financial Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Informasi Keuangan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Harga Perolehan:</strong>
                                <p class="fs-5 text-success">Rp {{ number_format($asset->acquisition_price, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="col-md-4">
                                <strong>Nilai Buku Saat Ini:</strong>
                                <p class="fs-5 text-primary">Rp {{ number_format($asset->book_value, 0, ',', '.') }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Penyusutan:</strong>
                                <p class="fs-5 text-danger">
                                    Rp {{ number_format($asset->acquisition_price - $asset->book_value, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>Tanggal Perolehan:</strong>
                                <p>{{ $asset->acquisition_date ? $asset->acquisition_date->format('d M Y') : '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Garansi Hingga:</strong>
                                <p>
                                    {{ $asset->warranty_until ? $asset->warranty_until->format('d M Y') : '-' }}
                                    @if($asset->warranty_until && $asset->warranty_until->isFuture())
                                        <span class="badge bg-success">Aktif</span>
                                    @elseif($asset->warranty_until)
                                        <span class="badge bg-danger">Kadaluarsa</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loan History -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Peminjaman</h5>
                    </div>
                    <div class="card-body">
                        @if($asset->loans->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Peminjam</th>
                                            <th>Status</th>
                                            <th>Tanggal Kembali</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($asset->loans->take(5) as $loan)
                                            <tr>
                                                <td>{{ $loan->loan_date->format('d M Y') }}</td>
                                                <td>{{ $loan->user->name ?? '-' }}</td>
                                                <td>
                                                    @php
                                                        $loanStatusLabels = [
                                                            'pending' => 'Menunggu',
                                                            'approved' => 'Disetujui',
                                                            'rejected' => 'Ditolak',
                                                            'returned' => 'Dikembalikan'
                                                        ];
                                                    @endphp
                                                    <span
                                                        class="badge bg-{{ $loan->status == 'approved' ? 'success' : 'warning' }}">
                                                        {{ $loanStatusLabels[$loan->status] ?? $loan->status }}
                                                    </span>
                                                </td>
                                                <td>{{ $loan->return_date ? $loan->return_date->format('d M Y') : '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center py-3">Belum ada riwayat peminjaman</p>
                        @endif
                    </div>
                </div>

                <!-- Maintenance History -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Riwayat Perawatan</h5>
                    </div>
                    <div class="card-body">
                        @if($asset->maintenances->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Tipe</th>
                                            <th>Deskripsi</th>
                                            <th>Biaya</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($asset->maintenances->take(5) as $maintenance)
                                            <tr>
                                                <td>{{ $maintenance->maintenance_date->format('d M Y') }}</td>
                                                <td>
                                                    @php
                                                        $typeLabels = [
                                                            'preventive' => 'Preventif',
                                                            'corrective' => 'Korektif',
                                                            'predictive' => 'Prediktif'
                                                        ];
                                                    @endphp
                                                    {{ $typeLabels[$maintenance->type] ?? $maintenance->type }}
                                                </td>
                                                <td>{{ Str::limit($maintenance->description, 30) }}</td>
                                                <td>Rp {{ number_format($maintenance->cost, 0, ',', '.') }}</td>
                                                <td>
                                                    @php
                                                        $maintenanceStatusLabels = [
                                                            'pending' => 'Menunggu',
                                                            'approved' => 'Disetujui',
                                                            'completed' => 'Selesai'
                                                        ];
                                                    @endphp
                                                    <span
                                                        class="badge bg-{{ $maintenance->status == 'completed' ? 'success' : 'warning' }}">
                                                        {{ $maintenanceStatusLabels[$maintenance->status] ?? $maintenance->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center py-3">Belum ada riwayat perawatan</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Photo -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-image me-2"></i>Foto</h5>
                    </div>
                    <div class="card-body text-center">
                        @if($asset->photo_path)
                            <img src="{{ asset('storage/' . $asset->photo_path) }}" class="img-fluid rounded"
                                alt="{{ $asset->name }}">
                        @else
                            <div class="bg-light p-5 rounded">
                                <i class="fas fa-image fa-5x text-muted"></i>
                                <p class="text-muted mt-3">Foto tidak tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- QR Code -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-qrcode me-2"></i>Kode QR</h5>
                    </div>
                    <div class="card-body text-center">
                        @if($asset->qr_code)
                            <div class="bg-light p-3 rounded mb-3">
                                <div id="qrcode" class="d-inline-block"></div>
                            </div>
                            <p class="small text-muted">{{ $asset->qr_code }}</p>
                            <button onclick="downloadQR()" class="btn btn-sm btn-primary">
                                <i class="fas fa-download me-1"></i>Unduh QR
                            </button>
                        @else
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-qrcode fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Kode QR belum dibuat</p>
                                <form action="{{ route('assets.qr', $asset->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-qrcode me-1"></i>Buat Kode QR
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik Cepat</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Peminjaman:</span>
                            <strong>{{ $asset->loans->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Peminjaman Aktif:</span>
                            <strong>{{ $asset->loans->where('status', 'approved')->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Perawatan:</span>
                            <strong>{{ $asset->maintenances->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Biaya Perawatan:</span>
                            <strong>Rp {{ number_format($asset->maintenances->sum('cost'), 0, ',', '.') }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Dibuat:</span>
                            <strong>{{ $asset->created_at->format('d M Y') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Terakhir Diperbarui:</span>
                            <strong>{{ $asset->updated_at->format('d M Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/qrcode-generator@1.4.4/qrcode.js"></script>
<script>
@if($asset->qr_code)
document.addEventListener('DOMContentLoaded', function() {
    const qrContainer = document.getElementById('qrcode');
    const logoUrl = '@php echo \App\Models\Setting::get("site_logo") ? asset("storage/" . \App\Models\Setting::get("site_logo")) : ""; @endphp';
    const qr = qrcode(0, 'H');
    qr.addData('{{ $asset->qr_code }}');
    qr.make();
    const canvas = document.createElement('canvas');
    const size = 280;
    canvas.width = size;
    canvas.height = size;
    const ctx = canvas.getContext('2d');
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, size, size);
    const moduleCount = qr.getModuleCount();
    const cellSize = size / moduleCount;
    const gradient = ctx.createLinearGradient(0, 0, size, size);
    gradient.addColorStop(0, '#3b82f6');
    gradient.addColorStop(1, '#1e40af');
    for (let row = 0; row < moduleCount; row++) {
        for (let col = 0; col < moduleCount; col++) {
            if (qr.isDark(row, col)) {
                const x = col * cellSize;
                const y = row * cellSize;
                const radius = cellSize * 0.35;
                ctx.fillStyle = gradient;
                ctx.beginPath();
                ctx.roundRect(x + cellSize * 0.05, y + cellSize * 0.05, cellSize * 0.9, cellSize * 0.9, radius);
                ctx.fill();
            }
        }
    }
    if (logoUrl) {
        const logo = new Image();
        logo.crossOrigin = 'anonymous';
        logo.onload = function() {
            const logoSize = size * 0.22;
            const logoX = (size - logoSize) / 2;
            const logoY = (size - logoSize) / 2;
            ctx.fillStyle = '#ffffff';
            ctx.beginPath();
            ctx.arc(size / 2, size / 2, logoSize * 0.65, 0, 2 * Math.PI);
            ctx.fill();
            ctx.strokeStyle = '#3b82f6';
            ctx.lineWidth = 4;
            ctx.stroke();
            ctx.save();
            ctx.beginPath();
            ctx.arc(size / 2, size / 2, logoSize * 0.55, 0, 2 * Math.PI);
            ctx.clip();
            ctx.drawImage(logo, logoX, logoY, logoSize, logoSize);
            ctx.restore();
            qrContainer.appendChild(canvas);
        };
        logo.onerror = function() { qrContainer.appendChild(canvas); };
        logo.src = logoUrl;
    } else {
        qrContainer.appendChild(canvas);
    }
});
function downloadQR() {
    const canvas = document.querySelector('#qrcode canvas');
    if (canvas) {
        canvas.toBlob(function(blob) {
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.download = 'QR-{{ $asset->asset_code }}.png';
            link.href = url;
            link.click();
            URL.revokeObjectURL(url);
        }, 'image/png');
    }
}
@endif
</script>
<style>
#qrcode {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 200px;
}
#qrcode canvas {
    max-width: 100%;
    height: auto !important;
    width: auto !important;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
    background: white;
    padding: 15px;
}
@media (max-width: 768px) {
    #qrcode canvas {
        max-width: 250px;
    }
}
</style>
@endpush
