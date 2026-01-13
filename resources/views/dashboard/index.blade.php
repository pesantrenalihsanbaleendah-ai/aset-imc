@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
    <style>
        .card-stat {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .card-stat .icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        @media (max-width: 768px) {
            .card-stat h2 {
                font-size: 1.5rem;
            }

            .card-stat h6 {
                font-size: 0.8rem;
            }
        }
    </style>

    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-2">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-stat shadow-sm border-left-primary h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Aset</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_assets ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <div class="icon bg-light-primary"><i class="fas fa-cube text-primary"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-stat shadow-sm border-left-warning h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Dalam Perawatan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $assets_in_maintenance ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <div class="icon bg-light-warning"><i class="fas fa-wrench text-warning"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-stat shadow-sm border-left-danger h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Kondisi Rusak</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $damaged_assets ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <div class="icon bg-light-danger"><i class="fas fa-exclamation-triangle text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-stat shadow-sm border-left-success h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Aset Baru (Bulan Ini)
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $new_assets ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <div class="icon bg-light-success"><i class="fas fa-plus-circle text-success"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin/Super Admin Dashboard -->
        @if(auth()->user()->hasAnyRole(['super_admin', 'admin_aset']))
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Status Aset</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="assetConditionChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Aset Per Kategori</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="assetCategoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card card-stat">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">Total Pengguna</h6>
                            <h2 class="mb-0">{{ $total_users ?? 0 }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card card-stat">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">Peminjaman Tertunda</h6>
                            <h2 class="mb-0 text-warning">{{ $pending_loans ?? 0 }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card card-stat">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">Peminjaman Overduc</h6>
                            <h2 class="mb-0 text-danger">{{ $overdue_loans ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Perlu Approval</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Peminjaman Pending:</strong> <span
                                    class="badge bg-warning">{{ $pending_approvals['loans'] ?? 0 }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Perawatan Pending:</strong> <span
                                    class="badge bg-warning">{{ $pending_approvals['maintenances'] ?? 0 }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Approver/Manager Dashboard -->
        @if(auth()->user()->hasRole('approver'))
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card card-stat">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">Peminjaman Pending</h6>
                            <h2 class="mb-0">{{ $pending_loans ?? 0 }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card card-stat">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">Perawatan Pending</h6>
                            <h2 class="mb-0">{{ $pending_maintenances ?? 0 }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card card-stat">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">Aset Bernilai Tinggi</h6>
                            <h2 class="mb-0">{{ $high_value_assets ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Staff Dashboard -->
        @if(auth()->user()->hasRole('staff'))
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card card-stat">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">Aset Saya</h6>
                            <h2 class="mb-0">{{ $my_assets ?? 0 }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card card-stat">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">Total Peminjaman</h6>
                            <h2 class="mb-0">{{ $my_loans ?? 0 }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card card-stat">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">Sedang Dipinjam</h6>
                            <h2 class="mb-0">{{ $my_borrowed ?? 0 }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card card-stat">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">Permintaan Pending</h6>
                            <h2 class="mb-0 text-warning">{{ $pending_requests ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            @if(!empty($my_asset_list) && $my_asset_list->count() > 0)
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Aset Saya</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Kode Aset</th>
                                    <th>Nama</th>
                                    <th>Kategori</th>
                                    <th>Lokasi</th>
                                    <th>Kondisi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($my_asset_list as $asset)
                                    <tr>
                                        <td><strong>{{ $asset->asset_code }}</strong></td>
                                        <td>{{ $asset->name }}</td>
                                        <td>{{ $asset->category->name ?? '-' }}</td>
                                        <td>{{ $asset->location->name ?? '-' }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $asset->condition === 'good' ? 'success' : ($asset->condition === 'acceptable' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($asset->condition) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $asset->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($asset->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif
    </div>

    @push('scripts')
        @if(auth()->user()->hasAnyRole(['super_admin', 'admin_aset']))
            <script>
                // Asset Condition Chart
                @if(!empty($asset_by_condition) && $asset_by_condition->count() > 0)
                    const conditionCtx = document.getElementById('assetConditionChart').getContext('2d');
                    new Chart(conditionCtx, {
                        type: 'doughnut',
                        data: {
                            labels: {!! json_encode($asset_by_condition->pluck('condition')->toArray()) !!},
                            datasets: [{
                                data: {!! json_encode($asset_by_condition->pluck('count')->toArray()) !!},
                                backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                @endif

                    // Asset Category Chart
                    @if(!empty($asset_by_category) && $asset_by_category->count() > 0)
                        const categoryCtx = document.getElementById('assetCategoryChart').getContext('2d');
                        new Chart(categoryCtx, {
                            type: 'bar',
                            data: {
                                labels: {!! json_encode($asset_by_category->map(function ($item) {
                                return $item->category ? $item->category->name : '-'; })->toArray()) !!},
                                datasets: [{
                                    label: 'Jumlah Aset',
                                    data: {!! json_encode($asset_by_category->pluck('count')->toArray()) !!},
                                    backgroundColor: '#3b82f6'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                indexAxis: 'y',
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                }
                            }
                        });
                    @endif
            </script>
        @endif
    @endpush
@endsection