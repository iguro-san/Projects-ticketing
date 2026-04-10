<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Event Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            color: #333;
            font-size: 1.8rem;
        }
        .login-header i {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            font-weight: 500;
            color: #555;
            margin-bottom: 0.5rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 0.75rem;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: none;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 1rem;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102,126,234,0.4);
        }
        .demo-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-calendar-alt"></i>
            <h1>Event Management</h1>
            <p class="text-muted">Silakan login untuk melanjutkan</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login.proses') }}" method="POST">
            @csrf
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" name="email" class="form-control" value="admin@example.com" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" class="form-control" value="123456" required>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>

        <div class="demo-info">
            <strong><i class="fas fa-info-circle"></i> Akun Demo:</strong><br>
            Admin: admin@example.com / 123456<br>
            User: user@example.com / 123456
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('events.index') }}" class="text-decoration-none">
                <i class="fas fa-arrow-left"></i> Kembali ke Events
            </a>
        </div>
    </div>
</body>
</html>