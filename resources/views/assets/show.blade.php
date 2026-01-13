@extends('layouts.app')

@section('title', 'Asset Details')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-box me-2"></i>Asset Details
            </h1>
            <div>
                <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
                <a href="{{ route('assets.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Asset Information -->
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Asset Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Asset Code:</strong>
                                <p class="text-primary fs-5">{{ $asset->asset_code }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Asset Name:</strong>
                                <p class="fs-5">{{ $asset->name }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Category:</strong>
                                <p><span class="badge bg-secondary">{{ $asset->category->name ?? '-' }}</span></p>
                            </div>
                            <div class="col-md-6">
                                <strong>Location:</strong>
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
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$asset->status] ?? 'secondary' }}">
                                        {{ ucfirst($asset->status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <strong>Condition:</strong>
                                <p>
                                    @php
                                        $conditionColors = [
                                            'good' => 'success',
                                            'acceptable' => 'warning',
                                            'poor' => 'danger'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $conditionColors[$asset->condition] ?? 'secondary' }}">
                                        {{ ucfirst($asset->condition) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Responsible User:</strong>
                                <p>{{ $asset->responsibleUser->name ?? 'Not Assigned' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Serial Number:</strong>
                                <p>{{ $asset->serial_number ?? '-' }}</p>
                            </div>
                        </div>

                        @if($asset->description)
                            <div class="mb-3">
                                <strong>Description:</strong>
                                <p class="text-muted">{{ $asset->description }}</p>
                            </div>
                        @endif

                        @if($asset->specification)
                            <div class="mb-3">
                                <strong>Specification:</strong>
                                <p class="text-muted">{{ $asset->specification }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Financial Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Financial Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Acquisition Price:</strong>
                                <p class="fs-5 text-success">Rp {{ number_format($asset->acquisition_price, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="col-md-4">
                                <strong>Current Book Value:</strong>
                                <p class="fs-5 text-primary">Rp {{ number_format($asset->book_value, 0, ',', '.') }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Depreciation:</strong>
                                <p class="fs-5 text-danger">
                                    Rp {{ number_format($asset->acquisition_price - $asset->book_value, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>Acquisition Date:</strong>
                                <p>{{ $asset->acquisition_date ? $asset->acquisition_date->format('d M Y') : '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Warranty Until:</strong>
                                <p>
                                    {{ $asset->warranty_until ? $asset->warranty_until->format('d M Y') : '-' }}
                                    @if($asset->warranty_until && $asset->warranty_until->isFuture())
                                        <span class="badge bg-success">Active</span>
                                    @elseif($asset->warranty_until)
                                        <span class="badge bg-danger">Expired</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loan History -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Loan History</h5>
                    </div>
                    <div class="card-body">
                        @if($asset->loans->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Borrower</th>
                                            <th>Status</th>
                                            <th>Return Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($asset->loans->take(5) as $loan)
                                            <tr>
                                                <td>{{ $loan->loan_date->format('d M Y') }}</td>
                                                <td>{{ $loan->user->name ?? '-' }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $loan->status == 'approved' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($loan->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $loan->return_date ? $loan->return_date->format('d M Y') : '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center py-3">No loan history</p>
                        @endif
                    </div>
                </div>

                <!-- Maintenance History -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Maintenance History</h5>
                    </div>
                    <div class="card-body">
                        @if($asset->maintenances->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Cost</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($asset->maintenances->take(5) as $maintenance)
                                            <tr>
                                                <td>{{ $maintenance->maintenance_date->format('d M Y') }}</td>
                                                <td>{{ ucfirst($maintenance->type) }}</td>
                                                <td>{{ Str::limit($maintenance->description, 30) }}</td>
                                                <td>Rp {{ number_format($maintenance->cost, 0, ',', '.') }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $maintenance->status == 'completed' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($maintenance->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center py-3">No maintenance history</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Photo -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-image me-2"></i>Photo</h5>
                    </div>
                    <div class="card-body text-center">
                        @if($asset->photo_path)
                            <img src="{{ asset('storage/' . $asset->photo_path) }}" class="img-fluid rounded"
                                alt="{{ $asset->name }}">
                        @else
                            <div class="bg-light p-5 rounded">
                                <i class="fas fa-image fa-5x text-muted"></i>
                                <p class="text-muted mt-3">No photo available</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- QR Code -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-qrcode me-2"></i>QR Code</h5>
                    </div>
                    <div class="card-body text-center">
                        @if($asset->qr_code)
                            <div class="bg-light p-3 rounded mb-3">
                                <div class="qr-code-placeholder bg-white p-4 d-inline-block border">
                                    <i class="fas fa-qrcode fa-5x text-dark"></i>
                                </div>
                            </div>
                            <p class="small text-muted">{{ $asset->qr_code }}</p>
                            <button class="btn btn-sm btn-primary">
                                <i class="fas fa-download me-1"></i>Download QR
                            </button>
                        @else
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-qrcode fa-3x text-muted mb-3"></i>
                                <p class="text-muted">QR Code not generated</p>
                                <form action="{{ route('assets.qr', $asset->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-qrcode me-1"></i>Generate QR Code
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Quick Stats</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Loans:</span>
                            <strong>{{ $asset->loans->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Active Loans:</span>
                            <strong>{{ $asset->loans->where('status', 'approved')->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Maintenances:</span>
                            <strong>{{ $asset->maintenances->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Maintenance Cost:</span>
                            <strong>Rp {{ number_format($asset->maintenances->sum('cost'), 0, ',', '.') }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Created:</span>
                            <strong>{{ $asset->created_at->format('d M Y') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Last Updated:</span>
                            <strong>{{ $asset->updated_at->format('d M Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection