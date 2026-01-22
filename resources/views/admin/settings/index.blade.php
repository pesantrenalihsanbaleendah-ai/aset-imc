@extends('layouts.app')

@section('title', 'Pengaturan Website')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cog me-2"></i>Pengaturan Website
        </h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- General Settings -->
        @if(isset($settings['general']))
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Pengaturan Umum</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($settings['general'] as $setting)
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ ucwords(str_replace('_', ' ', str_replace('site_', '', $setting->key))) }}</label>
                            @if($setting->type === 'textarea')
                                <textarea name="settings[{{ $setting->key }}]" class="form-control" rows="3">{{ $setting->value }}</textarea>
                            @else
                                <input type="text" name="settings[{{ $setting->key }}]" class="form-control" value="{{ $setting->value }}">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Appearance Settings -->
        @if(isset($settings['appearance']))
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-palette me-2"></i>Pengaturan Tampilan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($settings['appearance'] as $setting)
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                            
                            @if($setting->type === 'image')
                                @if($setting->value)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $setting->value) }}" alt="{{ $setting->key }}" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                                <input type="file" name="settings[{{ $setting->key }}]" class="form-control" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG. Max: 2MB</small>
                            @elseif($setting->type === 'color')
                                <input type="color" name="settings[{{ $setting->key }}]" class="form-control form-control-color" value="{{ $setting->value }}">
                            @else
                                <input type="text" name="settings[{{ $setting->key }}]" class="form-control" value="{{ $setting->value }}">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- WhatsApp Gateway Settings -->
        @if(isset($settings['whatsapp']))
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fab fa-whatsapp me-2"></i>Pengaturan WhatsApp Gateway</h5>
                <button type="button" class="btn btn-light btn-sm" onclick="event.preventDefault(); document.getElementById('test-wa-form').submit();">
                    <i class="fas fa-paper-plane me-1"></i>Test Koneksi
                </button>
            </div>
            <div class="card-body">
                <div class="alert alert-info py-2 small">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Interactive Approval:</strong> Balas dengan angka <b>1</b> pada chat WhatsApp (pribadi/grup) untuk menyetujui pengajuan terbaru secara otomatis.
                </div>
                
                <div class="row">
                    @foreach($settings['whatsapp'] as $setting)
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                {{ ucwords(str_replace('_', ' ', str_replace('whatsapp_', '', $setting->key))) }}
                            </label>
                            
                            @if($setting->type === 'boolean')
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="settings[{{ $setting->key }}]" class="form-check-input" 
                                           id="{{ $setting->key }}" {{ $setting->value == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $setting->key }}">
                                        {{ $setting->value == '1' ? 'Aktif' : 'Nonaktif' }}
                                    </label>
                                </div>
                            @elseif($setting->key === 'whatsapp_api_key')
                                <input type="password" name="settings[{{ $setting->key }}]" class="form-control" value="{{ $setting->value }}" 
                                       placeholder="API Key dari provider WhatsApp Gateway">
                            @else
                                <input type="text" name="settings[{{ $setting->key }}]" class="form-control" value="{{ $setting->value }}" 
                                       placeholder="{{ 
                                           $setting->key === 'whatsapp_endpoint' ? 'https://api.whatsapp-gateway.com/send' :
                                           ($setting->key === 'whatsapp_sender_number' ? '628123456789' :
                                           ($setting->key === 'whatsapp_receiver_number' ? '628987654321 atau 120363XXXXXX@g.us' : ''))
                                       }}">
                            @endif
                            
                            @if($setting->key === 'whatsapp_endpoint')
                                <small class="text-muted">URL endpoint API WhatsApp Gateway</small>
                            @elseif($setting->key === 'whatsapp_sender_number')
                                <small class="text-muted">Nomor pengirim (format: 628xxx)</small>
                            @elseif($setting->key === 'whatsapp_receiver_number')
                                <small class="text-muted">Nomor HP (628xxx) atau ID Grup WhatsApp</small>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Notification Settings -->
        @if(isset($settings['notification']))
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Pengaturan Notifikasi</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($settings['notification'] as $setting)
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="settings[{{ $setting->key }}]" class="form-check-input" 
                                       id="{{ $setting->key }}" {{ $setting->value == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="{{ $setting->key }}">
                                    {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Asset Settings -->
        @if(isset($settings['asset']))
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Pengaturan Aset</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($settings['asset'] as $setting)
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                            </label>
                            
                            @if($setting->type === 'boolean')
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="settings[{{ $setting->key }}]" class="form-check-input" 
                                           id="{{ $setting->key }}" {{ $setting->value == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $setting->key }}">
                                        {{ $setting->value == '1' ? 'Ya' : 'Tidak' }}
                                    </label>
                                </div>
                            @else
                                <input type="text" name="settings[{{ $setting->key }}]" class="form-control" value="{{ $setting->value }}">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i>Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Simpan Pengaturan
            </button>
        </div>
    </form>
</div>

<!-- Hidden form for testing WhatsApp -->
<form id="test-wa-form" action="{{ route('admin.settings.test-whatsapp') }}" method="POST" class="d-none">
    @csrf
</form>
@endsection
