@extends('layouts.app')

@section('title', 'Edit Peminjaman')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit me-2"></i>Edit Pengajuan Peminjaman
            </h1>
            <a href="{{ route('loans.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('loans.update', $loan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <h5 class="mb-3 text-primary">Informasi Peminjaman</h5>

                            <div class="mb-3">
                                <label class="form-label">Aset yang Dipinjam <span class="text-danger">*</span></label>
                                <select name="asset_id" class="form-select @error('asset_id') is-invalid @enderror"
                                    required>
                                    <option value="">Pilih Aset</option>
                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->id }}" {{ old('asset_id', $loan->asset_id) == $asset->id ? 'selected' : '' }}>
                                            {{ $asset->asset_code }} - {{ $asset->name }} ({{ $asset->location->name ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('asset_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Pengaju <span class="text-danger">*</span></label>
                                <input type="text" name="requester_name" class="form-control @error('requester_name') is-invalid @enderror"
                                    value="{{ old('requester_name', $loan->requester_name) }}" placeholder="Masukkan nama pengaju" required>
                                @error('requester_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Nama orang yang mengajukan peminjaman</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Penanggung Jawab <span class="text-danger">*</span></label>
                                <input type="text" name="responsible_person" class="form-control @error('responsible_person') is-invalid @enderror"
                                    value="{{ old('responsible_person', $loan->responsible_person) }}" placeholder="Masukkan nama penanggung jawab" required>
                                @error('responsible_person')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tujuan Peminjaman <span class="text-danger">*</span></label>
                                <textarea name="purpose" class="form-control @error('purpose') is-invalid @enderror"
                                    rows="4" required
                                    placeholder="Jelaskan tujuan peminjaman aset ini">{{ old('purpose', $loan->purpose) }}</textarea>
                                @error('purpose')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <h5 class="mb-3 text-primary">Jadwal Peminjaman</h5>

                            <div class="mb-3">
                                <label class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                                <input type="date" name="loan_date"
                                    class="form-control @error('loan_date') is-invalid @enderror"
                                    value="{{ old('loan_date', $loan->loan_date ? $loan->loan_date->format('Y-m-d') : '') }}"
                                    required>
                                @error('loan_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tanggal Pengembalian <span class="text-danger">*</span></label>
                                <input type="date" name="expected_return_date"
                                    class="form-control @error('expected_return_date') is-invalid @enderror"
                                    value="{{ old('expected_return_date', $loan->expected_return_date ? $loan->expected_return_date->format('Y-m-d') : '') }}"
                                    required>
                                @error('expected_return_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tanggal rencana pengembalian aset</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Catatan</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                    placeholder="Catatan tambahan (opsional)">{{ old('notes', $loan->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Dokumen Pendukung</label>
                                @if($loan->document_path)
                                    <div class="mb-2">
                                        <a href="{{ asset('storage/' . $loan->document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-file-download me-1"></i>Lihat Dokumen Saat Ini
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="document"
                                    class="form-control @error('document') is-invalid @enderror" 
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.webp">
                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG, GIF, WEBP. Max: 2MB</small>
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
                            <li>Pastikan data peminjaman sudah benar sebelum menyimpan</li>
                            <li>Perubahan akan menunggu persetujuan ulang jika status masih pending</li>
                            <li>Pastikan mengembalikan aset sesuai tanggal yang dijanjikan</li>
                        </ul>
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