<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Capstone 2026</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container" style="max-width: 400px;">
        <div class="card border-0 shadow-sm p-4 rounded-4">
            <h4 class="fw-bold text-center mb-1">Welcome Back</h4>
            <p class="text-muted small text-center mb-4">Masuk untuk mengakses sistem</p>

            @if($errors->any())
                <div class="alert alert-danger small p-2">{{ $errors->first() }}</div>
            @endif

            <form action="/login" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" required placeholder="admin@test.com">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-warning text-white w-100 fw-bold">Login</button>
            </form>
        </div>
    </div>
</body>
</html>