@extends('layouts.app')

@section('title', 'Ajukan Peminjaman')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus-circle me-2"></i>Ajukan Peminjaman Aset
            </h1>
            <a href="{{ route('loans.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('loans.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">Informasi Peminjaman</h5>

                            <div class="mb-3">
                                <label class="form-label">Aset yang Dipinjam <span class="text-danger">*</span></label>
                                <select name="asset_id" class="form-select @error('asset_id') is-invalid @enderror"
                                    required>
                                    <option value="">Pilih Aset</option>
                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                            {{ $asset->asset_code }} - {{ $asset->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('asset_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Peminjam <span class="text-danger">*</span></label>
                                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                    <option value="">Pilih Peminjam</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', auth()->id()) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Penanggung Jawab <span class="text-danger">*</span></label>
                                <input type="text" name="responsible_person" class="form-control @error('responsible_person') is-invalid @enderror"
                                    value="{{ old('responsible_person') }}" placeholder="Masukkan nama penanggung jawab" required>
                                @error('responsible_person')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tujuan Peminjaman <span class="text-danger">*</span></label>
                                <textarea name="purpose" class="form-control @error('purpose') is-invalid @enderror"
                                    rows="4" required
                                    placeholder="Jelaskan tujuan peminjaman aset ini">{{ old('purpose') }}</textarea>
                                @error('purpose')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">Jadwal Peminjaman</h5>

                            <div class="mb-3">
                                <label class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                                <input type="date" name="loan_date"
                                    class="form-control @error('loan_date') is-invalid @enderror"
                                    value="{{ old('loan_date', date('Y-m-d')) }}" required>
                                @error('loan_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tanggal Pengembalian <span class="text-danger">*</span></label>
                                <input type="date" name="expected_return_date"
                                    class="form-control @error('expected_return_date') is-invalid @enderror"
                                    value="{{ old('expected_return_date') }}" required>
                                @error('expected_return_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tanggal rencana pengembalian aset</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Catatan</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                    placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Dokumen Pendukung</label>
                                <input type="file" name="document"
                                    class="form-control @error('document') is-invalid @enderror" accept=".pdf,.doc,.docx">
                                <small class="text-muted">Format: PDF, DOC, DOCX. Max: 2MB</small>
                                @error('document')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Informasi</h6>
                        <ul class="mb-0">
                            <li>Peminjaman akan berstatus <strong>Pending</strong> dan menunggu persetujuan</li>
                            <li>Anda akan diberitahu ketika peminjaman disetujui atau ditolak</li>
                            <li>Pastikan mengembalikan aset sesuai tanggal yang dijanjikan</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('loans.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i>Ajukan Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection