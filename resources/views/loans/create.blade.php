@extends('layouts.app')

@section('title', 'Ajukan Peminjaman')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }
</style>
@endpush

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
                                <select name="asset_ids[]" id="asset-select" class="form-select @error('asset_ids') is-invalid @enderror @error('asset_ids.*') is-invalid @enderror"
                                    multiple="multiple" required>
                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->id }}" 
                                            {{ (is_array(old('asset_ids')) && in_array($asset->id, old('asset_ids'))) ? 'selected' : '' }}>
                                            {{ $asset->asset_code }} - {{ $asset->name }} ({{ $asset->location->name ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Anda dapat memilih lebih dari satu aset</small>
                                @error('asset_ids')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @error('asset_ids.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Pengaju <span class="text-danger">*</span></label>
                                <input type="text" name="requester_name" class="form-control @error('requester_name') is-invalid @enderror"
                                    value="{{ old('requester_name') }}" placeholder="Masukkan nama pengaju" required>
                                @error('requester_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Nama orang yang mengajukan peminjaman</small>
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
                                <input type="file" name="document" id="document-input"
                                    class="form-control @error('document') is-invalid @enderror" 
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.webp"
                                    onchange="previewDocument(this)">
                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG, GIF, WEBP. Max: 2MB</small>
                                @error('document')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="document-preview" class="mt-2" style="display: none;">
                                    <img id="preview-image" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                </div>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 for multiple asset selection
        $('#asset-select').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih satu atau lebih aset',
            allowClear: true,
            width: '100%'
        });
    });

    // Preview document if it's an image
    function previewDocument(input) {
        const preview = document.getElementById('document-preview');
        const previewImage = document.getElementById('preview-image');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const fileType = file.type;
            
            // Check if file is an image
            if (fileType.startsWith('image/')) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    preview.style.display = 'block';
                };
                
                reader.readAsDataURL(file);
            } else {
                // Hide preview for non-image files
                preview.style.display = 'none';
            }
        } else {
            preview.style.display = 'none';
        }
    }
</script>
@endpush