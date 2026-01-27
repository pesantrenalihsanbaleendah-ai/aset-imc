<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $asset->name }} - Detail Aset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3b82f6;
            --secondary-color: #1e40af;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .asset-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 600px;
            margin: 0 auto;
        }

        .asset-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px;
            text-align: center;
        }

        .asset-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .asset-code {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 20px;
            border-radius: 50px;
            display: inline-block;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .asset-photo {
            width: 100%;
            height: 300px;
            object-fit: cover;
            background: #f3f4f6;
        }

        .asset-body {
            padding: 30px;
        }

        .info-group {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-group:last-child {
            border-bottom: none;
        }

        .info-label {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 1.1rem;
            color: #111827;
            font-weight: 500;
        }

        .badge-custom {
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .footer-info {
            background: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo-container img {
            max-width: 120px;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="asset-card">
            <!-- Header -->
            <div class="asset-header">
                @php
                    $siteLogo = \App\Models\Setting::get('site_logo');
                    $siteName = \App\Models\Setting::get('site_name');
                @endphp

                @if($siteLogo)
                    <div class="logo-container">
                        <img src="{{ asset('storage/' . $siteLogo) }}" alt="{{ $siteName }}">
                    </div>
                @endif

                <h1>{{ $asset->name }}</h1>
                <div class="asset-code">
                    <i class="fas fa-qrcode me-2"></i>{{ $asset->asset_code }}
                </div>
            </div>

            <!-- Photo -->
            @if($asset->photo_path)
                <img src="{{ asset('storage/' . $asset->photo_path) }}" alt="{{ $asset->name }}" class="asset-photo">
            @else
                <div class="asset-photo d-flex align-items-center justify-content-center">
                    <i class="fas fa-image fa-5x text-muted"></i>
                </div>
            @endif

            <!-- Body -->
            <div class="asset-body">
                <!-- Category & Location -->
                <div class="row mb-4">
                    <div class="col-6">
                        <div class="info-label">
                            <i class="fas fa-folder me-1"></i>Kategori
                        </div>
                        <div class="info-value">
                            <span class="badge bg-secondary badge-custom">{{ $asset->category->name ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">
                            <i class="fas fa-map-marker-alt me-1"></i>Lokasi
                        </div>
                        <div class="info-value">{{ $asset->location->name ?? '-' }}</div>
                    </div>
                </div>

                <!-- Quantity -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="info-label">
                            <i class="fas fa-boxes me-1"></i>Jumlah Aset
                        </div>
                        <div class="info-value">
                            <span class="badge bg-info badge-custom">{{ $asset->quantity ?? 1 }} Unit</span>
                        </div>
                    </div>
                </div>

                <!-- Status & Condition -->
                <div class="row mb-4">
                    <div class="col-6">
                        <div class="info-label">
                            <i class="fas fa-info-circle me-1"></i>Status
                        </div>
                        <div class="info-value">
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
                            <span class="badge bg-{{ $statusColors[$asset->status] ?? 'secondary' }} badge-custom">
                                {{ $statusLabels[$asset->status] ?? $asset->status }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">
                            <i class="fas fa-check-circle me-1"></i>Kondisi
                        </div>
                        <div class="info-value">
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
                            <span
                                class="badge bg-{{ $conditionColors[$asset->condition] ?? 'secondary' }} badge-custom">
                                {{ $conditionLabels[$asset->condition] ?? $asset->condition }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if($asset->description)
                    <div class="info-group">
                        <div class="info-label">
                            <i class="fas fa-align-left me-1"></i>Deskripsi
                        </div>
                        <div class="info-value">{{ $asset->description }}</div>
                    </div>
                @endif

                <!-- Specification -->
                @if($asset->specification)
                    <div class="info-group">
                        <div class="info-label">
                            <i class="fas fa-list me-1"></i>Spesifikasi
                        </div>
                        <div class="info-value">{{ $asset->specification }}</div>
                    </div>
                @endif

                <!-- Responsible User -->
                @if($asset->responsibleUser)
                    <div class="info-group">
                        <div class="info-label">
                            <i class="fas fa-user me-1"></i>Penanggung Jawab
                        </div>
                        <div class="info-value">{{ $asset->responsibleUser->name }}</div>
                    </div>
                @endif

                <!-- Serial Number -->
                @if($asset->serial_number)
                    <div class="info-group">
                        <div class="info-label">
                            <i class="fas fa-barcode me-1"></i>Nomor Seri
                        </div>
                        <div class="info-value">{{ $asset->serial_number }}</div>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="footer-info">
                <i class="fas fa-building me-2"></i>
                {{ $siteName ?? 'Sistem Manajemen Aset' }}
                <br>
                <small class="text-muted">Dipindai pada {{ now()->format('d M Y, H:i') }} WIB</small>
            </div>
        </div>
    </div>
</body>

</html>