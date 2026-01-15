@extends('layouts.app')

@section('title', 'Edit Peminjaman')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Aset yang Dipinjam <span class="text-danger">*</span></label>
                                <select name="asset_id" class="form-select @error('asset_id') is-invalid @enderror"
                                    required>
                                    <option value="">Pilih Aset</option>
                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->id }}" {{ old('asset_id', $loan->asset_id) == $asset->id ? 'selected' : '' }}>
                                            [{{ $asset->code }}] {{ $asset->name }}
                                            ({{ $asset->status == 'available' ? 'Tersedia' : $asset->status }})
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
                                    <option value="">Pilih Member</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $loan->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->employee_id ?? 'No ID' }})
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
                                    value="{{ old('responsible_person', $loan->responsible_person) }}" placeholder="Masukkan nama penanggung jawab" required>
                                @error('responsible_person')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                        <input type="date" name="loan_date"
                                            class="form-control @error('loan_date') is-invalid @enderror"
                                            value="{{ old('loan_date', $loan->loan_date ? $loan->loan_date->format('Y-m-d') : '') }}"
                                            required>
                                        @error('loan_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Kembali (Estimasi) <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="due_date"
                                            class="form-control @error('due_date') is-invalid @enderror"
                                            value="{{ old('due_date', $loan->due_date ? $loan->due_date->format('Y-m-d') : '') }}"
                                            required>
                                        @error('due_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tujuan Peminjaman <span class="text-danger">*</span></label>
                                <textarea name="purpose" class="form-control @error('purpose') is-invalid @enderror"
                                    rows="3" required
                                    placeholder="Jelaskan tujuan peminjaman aset...">{{ old('purpose', $loan->purpose) }}</textarea>
                                @error('purpose')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Dokumen Pendukung (Opsional)</label>
                                <input type="file" name="attachment"
                                    class="form-control @error('attachment') is-invalid @enderror">
                                @if($loan->attachment)
                                    <div class="mt-2">
                                        <small class="text-muted">File saat ini: </small>
                                        <a href="{{ asset('storage/' . $loan->attachment) }}" target="_blank" class="small">
                                            <i class="fas fa-paperclip me-1"></i>Lihat Lampiran
                                        </a>
                                    </div>
                                @endif
                                @error('attachment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format: PDF, JPG, PNG (Maks. 2MB)</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Catatan Tambahan</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                    rows="2">{{ old('notes', $loan->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
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