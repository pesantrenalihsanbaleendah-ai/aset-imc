<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        $siteName = 'Aset IMC';
        $siteDescription = 'Sistem Inventarisasi Aset';
        $siteLogo = null;
        $siteFavicon = null;

        try {
            $nameSetting = \App\Models\Setting::where('key', 'site_name')->first();
            $descSetting = \App\Models\Setting::where('key', 'site_description')->first();
            $logoSetting = \App\Models\Setting::where('key', 'site_logo')->first();
            $faviconSetting = \App\Models\Setting::where('key', 'site_favicon')->first();

            if ($nameSetting && $nameSetting->value) {
                $siteName = $nameSetting->value;
            }

            if ($descSetting && $descSetting->value) {
                $siteDescription = $descSetting->value;
            }

            if ($logoSetting && $logoSetting->value) {
                $siteLogo = $logoSetting->value;
            }

            if ($faviconSetting && $faviconSetting->value) {
                $siteFavicon = $faviconSetting->value;
            } elseif ($logoSetting && $logoSetting->value) {
                $siteFavicon = $logoSetting->value;
            }
        } catch (\Exception $e) {
            // Keep defaults
        }
    @endphp

    <title>Login - {{ $siteName }}</title>

    {{-- Dynamic Favicon --}}
    @if($siteFavicon)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $siteFavicon) }}">
    @else
        <link rel="icon"
            href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📦</text></svg>">
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, #1e40af 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary) 0%, #1e40af 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .login-header .logo-container {
            margin-bottom: 15px;
        }

        .login-header .logo-container img {
            max-height: 80px;
            max-width: 200px;
            object-fit: contain;
        }

        .login-header i {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .login-header h2 {
            margin: 0;
            font-weight: 600;
        }

        .login-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-label {
            font-weight: 500;
            color: #334155;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .input-group-text {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-right: none;
            border-radius: 8px 0 0 8px;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 8px 8px 0;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary) 0%, #1e40af 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
            color: white;
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        .login-footer {
            text-align: center;
            padding: 20px;
            background: #f8fafc;
            color: #64748b;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                @if($siteLogo)
                    <div class="logo-container">
                        <img src="{{ asset('storage/' . $siteLogo) }}" alt="{{ $siteName }}">
                    </div>
                @else
                    <i class="fas fa-cube"></i>
                @endif
                <h2>{{ $siteName }}</h2>
                <p>{{ $siteDescription }}</p>
            </div>

            <div class="login-body">
                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success mb-4">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-1"></i> Email
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-1"></i> Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-key"></i>
                            </span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required placeholder="Masukkan password">
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">
                                Ingat saya
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i> Login
                    </button>
                </form>
            </div>

            <div class="login-footer">
                <p class="mb-0">&copy; {{ date('Y') }} Aset IMC. All rights reserved.</p>
            </div>
        </div>

        <div class="text-center mt-3">
            <p class="text-white">
                <small>
                    <i class="fas fa-info-circle me-1"></i>
                    Gunakan kredensial yang telah diberikan untuk login
                </small>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>