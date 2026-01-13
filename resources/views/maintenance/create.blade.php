@extends('layouts.app')

@section('title', 'Ajukan Pemeliharaan')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus-circle me-2"></i>Ajukan Pemeliharaan Aset
            </h1>
            <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('maintenance.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">Informasi Aset</h5>

                            <div class="mb-3">
                                <label class="form-label">Aset <span class="text-danger">*</span></label>
                                <select name="asset_id" class="form-select @error('asset_id') is-invalid @enderror"
                                    required>
                                    <option value="">Pilih Aset</option>
                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                            {{ $asset->asset_code }} - {{ $asset->name }} ({{ ucfirst($asset->status) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('asset_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tipe Pemeliharaan <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="preventive" {{ old('type') == 'preventive' ? 'selected' : '' }}>
                                        Preventif - Pemeliharaan Berkala
                                    </option>
                                    <option value="corrective" {{ old('type') == 'corrective' ? 'selected' : '' }}>
                                        Korektif - Perbaikan Kerusakan
                                    </option>
                                    <option value="predictive" {{ old('type') == 'predictive' ? 'selected' : '' }}>
                                        Prediktif - Berdasarkan Monitoring
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tanggal Dijadwalkan <span class="text-danger">*</span></label>
                                <input type="date" name="scheduled_date"
                                    class="form-control @error('scheduled_date') is-invalid @enderror"
                                    value="{{ old('scheduled_date') }}" required>
                                @error('scheduled_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tanggal rencana pemeliharaan</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">Detail Pemeliharaan</h5>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi Masalah/Kebutuhan <span
                                        class="text-danger">*</span></label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                    rows="5" required
                                    placeholder="Jelaskan masalah atau kebutuhan pemeliharaan">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Estimasi Biaya</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="cost"
                                        class="form-control @error('cost') is-invalid @enderror"
                                        value="{{ old('cost', 0) }}" min="0" step="0.01" placeholder="0">
                                    @error('cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Estimasi biaya (opsional, bisa diisi saat selesai)</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Dokumen Pendukung</label>
                                <input type="file" name="document"
                                    class="form-control @error('document') is-invalid @enderror"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG. Max: 2MB</small>
                                @error('document')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Informasi Tipe Pemeliharaan</h6>
                        <ul class="mb-0">
                            <li><strong>Preventif:</strong> Pemeliharaan rutin untuk mencegah kerusakan</li>
                            <li><strong>Korektif:</strong> Perbaikan atas kerusakan yang sudah terjadi</li>
                            <li><strong>Prediktif:</strong> Pemeliharaan berdasarkan hasil monitoring kondisi aset</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i>Ajukan Pemeliharaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection