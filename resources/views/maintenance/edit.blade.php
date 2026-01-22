@extends('layouts.app')

@section('title', 'Edit Perawatan')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit me-2"></i>Edit Pengajuan Perawatan
            </h1>
            <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('maintenance.update', $maintenance->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Aset yang Dirawat <span class="text-danger">*</span></label>
                                <select name="asset_id" class="form-select @error('asset_id') is-invalid @enderror"
                                    required>
                                    <option value="">Pilih Aset</option>
                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->id }}" {{ old('asset_id', $maintenance->asset_id) == $asset->id ? 'selected' : '' }}>
                                            [{{ $asset->code }}] {{ $asset->name }} ({{ $asset->status }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('asset_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tipe Perawatan <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="routine" {{ old('type', $maintenance->type) == 'routine' ? 'selected' : '' }}>Rutin (Routine)</option>
                                    <option value="repair" {{ old('type', $maintenance->type) == 'repair' ? 'selected' : '' }}>Perbaikan (Repair)</option>
                                    <option value="upgrade" {{ old('type', $maintenance->type) == 'upgrade' ? 'selected' : '' }}>Peningkatan (Upgrade)</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jadwal Mulai Perawatan <span class="text-danger">*</span></label>
                                <input type="date" name="start_date"
                                    class="form-control @error('start_date') is-invalid @enderror"
                                    value="{{ old('start_date', $maintenance->start_date ? $maintenance->start_date->format('Y-m-d') : '') }}"
                                    required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Masalah / Deskripsi <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                    rows="4" required
                                    placeholder="Jelaskan detail perawatan atau masalah pada aset...">{{ old('description', $maintenance->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Vendor / Teknisi</label>
                                <input type="text" name="vendor" class="form-control @error('vendor') is-invalid @enderror"
                                    value="{{ old('vendor', $maintenance->vendor) }}"
                                    placeholder="Contoh: PT. Servis Maju, Bp. Ahmad">
                                @error('vendor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Estimasi Biaya</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="cost"
                                        class="form-control @error('cost') is-invalid @enderror"
                                        value="{{ old('cost', $maintenance->cost) }}" placeholder="0">
                                </div>
                                @error('cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex flex-wrap justify-content-end gap-2">
                        <button type="reset" class="btn btn-light">
                            <i class="fas fa-undo me-1"></i>Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection