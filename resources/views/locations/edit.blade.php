@extends('layouts.app')

@section('title', 'Edit Lokasi')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit me-2"></i>Edit Lokasi
            </h1>
            <a href="{{ route('locations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('locations.update', $location->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">Informasi Dasar</h5>

                            <div class="mb-3">
                                <label class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $location->name) }}" required placeholder="Contoh: Ruang Server">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Parent Lokasi</label>
                                <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                    <option value="">Tidak Ada (Lokasi Utama)</option>
                                    @foreach($parentLocations as $parent)
                                        @if($parent->id != $location->id)
                                            <option value="{{ $parent->id }}" {{ old('parent_id', $location->parent_id) == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Pilih parent jika lokasi ini adalah sub-lokasi</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Level <span class="text-danger">*</span></label>
                                <select name="level" class="form-select @error('level') is-invalid @enderror" required>
                                    <option value="">Pilih Level</option>
                                    <option value="building" {{ old('level', $location->level) == 'building' ? 'selected' : '' }}>Gedung
                                    </option>
                                    <option value="floor" {{ old('level', $location->level) == 'floor' ? 'selected' : '' }}>Lantai</option>
                                    <option value="room" {{ old('level', $location->level) == 'room' ? 'selected' : '' }}>Ruangan</option>
                                    <option value="other" {{ old('level', $location->level) == 'other' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                    rows="3" placeholder="Alamat lengkap lokasi">{{ old('address', $location->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">Detail Lokasi</h5>

                            <div class="mb-3">
                                <label class="form-label">Gedung</label>
                                <input type="text" name="building"
                                    class="form-control @error('building') is-invalid @enderror"
                                    value="{{ old('building', $location->building) }}" placeholder="Contoh: Gedung A">
                                @error('building')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Lantai</label>
                                <input type="text" name="floor" class="form-control @error('floor') is-invalid @enderror"
                                    value="{{ old('floor', $location->floor) }}" placeholder="Contoh: Lantai 2">
                                @error('floor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ruangan</label>
                                <input type="text" name="room" class="form-control @error('room') is-invalid @enderror"
                                    value="{{ old('room', $location->room) }}" placeholder="Contoh: R.201">
                                @error('room')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info mt-4">
                                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Contoh Hierarki Lokasi</h6>
                                <ul class="mb-0 small">
                                    <li>Gedung A (Level: Gedung, Parent: -)</li>
                                    <li>└─ Lantai 2 (Level: Lantai, Parent: Gedung A)</li>
                                    <li>&nbsp;&nbsp;&nbsp;&nbsp;└─ Ruang Server (Level: Ruangan, Parent: Lantai 2)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex flex-wrap justify-content-end gap-2">
                        <button type="reset" class="btn btn-light">
                            <i class="fas fa-undo me-1"></i>Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Lokasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection