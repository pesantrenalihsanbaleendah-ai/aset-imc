@extends('layouts.app')

@section('title', 'Add New Asset')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus-circle me-2"></i>Add New Asset
            </h1>
            <a href="{{ route('assets.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to List
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-lg-6 mb-3">
                            <h5 class="mb-3 text-primary">Basic Information</h5>

                            <div class="mb-3">
                                <label class="form-label">Asset Code <span class="text-danger">*</span></label>
                                <input type="text" name="asset_code"
                                    class="form-control @error('asset_code') is-invalid @enderror"
                                    value="{{ old('asset_code', $nextCode) }}" required>
                                @error('asset_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Asset Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jumlah Aset <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                                    value="{{ old('quantity', 1) }}" min="1" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Masukkan jumlah unit aset yang tersedia</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror"
                                    required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Location <span class="text-danger">*</span></label>
                                <select name="location_id" class="form-select @error('location_id') is-invalid @enderror"
                                    required>
                                    <option value="">Select Location</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('location_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Responsible User</label>
                                <select name="responsible_user_id"
                                    class="form-select @error('responsible_user_id') is-invalid @enderror">
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('responsible_user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('responsible_user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                    rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Financial & Technical Details -->
                        <div class="col-lg-6 mb-3">
                            <h5 class="mb-3 text-primary">Financial & Technical Details</h5>

                            <div class="mb-3">
                                <label class="form-label">Acquisition Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="acquisition_price"
                                        class="form-control @error('acquisition_price') is-invalid @enderror"
                                        value="{{ old('acquisition_price', 0) }}" min="0" step="0.01" required>
                                    @error('acquisition_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Book Value <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="book_value"
                                        class="form-control @error('book_value') is-invalid @enderror"
                                        value="{{ old('book_value', 0) }}" min="0" step="0.01" required>
                                    @error('book_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Condition <span class="text-danger">*</span></label>
                                <select name="condition" class="form-select @error('condition') is-invalid @enderror"
                                    required>
                                    <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Good</option>
                                    <option value="acceptable" {{ old('condition') == 'acceptable' ? 'selected' : '' }}>
                                        Acceptable</option>
                                    <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                                </select>
                                @error('condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>
                                        Maintenance</option>
                                    <option value="damaged" {{ old('status') == 'damaged' ? 'selected' : '' }}>Damaged
                                    </option>
                                    <option value="disposed" {{ old('status') == 'disposed' ? 'selected' : '' }}>Disposed
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Acquisition Date</label>
                                <input type="date" name="acquisition_date"
                                    class="form-control @error('acquisition_date') is-invalid @enderror"
                                    value="{{ old('acquisition_date') }}">
                                @error('acquisition_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Warranty Until</label>
                                <input type="date" name="warranty_until"
                                    class="form-control @error('warranty_until') is-invalid @enderror"
                                    value="{{ old('warranty_until') }}">
                                @error('warranty_until')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Serial Number</label>
                                <input type="text" name="serial_number"
                                    class="form-control @error('serial_number') is-invalid @enderror"
                                    value="{{ old('serial_number') }}">
                                @error('serial_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Specification</label>
                                <textarea name="specification"
                                    class="form-control @error('specification') is-invalid @enderror"
                                    rows="3">{{ old('specification') }}</textarea>
                                @error('specification')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Photo</label>
                                <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror"
                                    accept="image/*">
                                <small class="text-muted">Max size: 2MB</small>
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex flex-wrap justify-content-end gap-2">
                        <a href="{{ route('assets.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection