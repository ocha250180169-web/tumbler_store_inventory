<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5 col-lg-4">
            <div class="text-center mb-4">
                <div class="fs-1">🥤</div>
                <h1 class="h4 fw-bold text-success">{{ config('app.name') }}</h1>
                <p class="text-muted small mb-0">Sistem Inventory &amp; Penjualan Tumbler</p>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    @if (session('success'))
                        <div class="alert alert-success py-2">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login.attempt') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Masuk</button>
                    </form>
                </div>
            </div>

            <p class="text-center text-muted small mt-3">
                Akun demo: <code>admin@tumbler.test</code> / <code>staff@tumbler.test</code><br>
                Password: <code>password123</code>
            </p>
        </div>
    </div>
</div>
</body>
</html>
