@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus-circle me-2"></i>Tambah Kategori Aset
            </h1>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kode Kategori <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                    value="{{ old('code') }}" required placeholder="Contoh: KAT-001">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kode unik untuk kategori ini</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" required placeholder="Contoh: Komputer & Laptop">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                    rows="4" placeholder="Deskripsi kategori (opsional)">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Pengaturan Depresiasi</h6>
                                <p class="mb-0 small">Pengaturan ini akan digunakan untuk menghitung depresiasi aset dalam
                                    kategori ini.</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Metode Depresiasi</label>
                                <select name="depreciation_method"
                                    class="form-select @error('depreciation_method') is-invalid @enderror">
                                    <option value="">Pilih Metode</option>
                                    <option value="straight_line" {{ old('depreciation_method') == 'straight_line' ? 'selected' : '' }}>
                                        Garis Lurus (Straight Line)
                                    </option>
                                    <option value="declining_balance" {{ old('depreciation_method') == 'declining_balance' ? 'selected' : '' }}>
                                        Saldo Menurun (Declining Balance)
                                    </option>
                                    <option value="units_of_production" {{ old('depreciation_method') == 'units_of_production' ? 'selected' : '' }}>
                                        Unit Produksi (Units of Production)
                                    </option>
                                </select>
                                @error('depreciation_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Masa Manfaat (Tahun)</label>
                                <input type="number" name="depreciation_years"
                                    class="form-control @error('depreciation_years') is-invalid @enderror"
                                    value="{{ old('depreciation_years') }}" min="1" max="50" placeholder="Contoh: 5">
                                @error('depreciation_years')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Estimasi masa manfaat aset dalam tahun</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex flex-wrap justify-content-end gap-2">
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Simpan Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection