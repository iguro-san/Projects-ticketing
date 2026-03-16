<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Event</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .navbar {
            background: white;
            border-radius: 10px;
            padding: 15px 30px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        .navbar-brand {
            font-size: 1.3em;
            font-weight: bold;
            color: #667eea;
        }
        .navbar-menu {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .navbar-menu a {
            color: #666;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 5px;
            transition: all 0.3s;
            font-weight: 500;
        }
        .navbar-menu a:hover {
            background: #667eea;
            color: white;
        }
        .navbar-menu .btn-dashboard {
            background: #667eea;
            color: white;
        }
        .navbar-menu .btn-dashboard:hover {
            background: #764ba2;
        }
        .navbar-menu .btn-logout {
            background: #dc3545;
            color: white;
        }
        .navbar-menu .btn-logout:hover {
            background: #c82333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2.5em;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        .event-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid #667eea;
            transition: transform 0.3s;
        }
        .event-card:hover {
            transform: translateX(10px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .event-title {
            color: #333;
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        .event-id {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .event-date {
            color: #666;
            margin-bottom: 15px;
        }
        .btn-detail {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .btn-detail:hover {
            background: #764ba2;
        }
        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
        .user-welcome {
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <!-- NAVBAR dengan posisi yang sudah ditukar -->
    <div class="navbar">
        <div class="navbar-brand">
            🎫 Event UMKM
        </div>
        <div class="navbar-menu">
            @if(session()->has('logged_in'))
                <span class="user-welcome">Halo, {{ session('user_name') }}</span>
                <!-- LOGOUT dulu, baru DASHBOARD -->
                <a href="/logout" class="btn-logout">Logout</a>
                <a href="/dashboard" class="btn-dashboard">Dashboard</a>
            @else
                <!-- DASHBOARD dulu, baru LOGIN -->
                <a href="/dashboard">Dashboard</a>
                <a href="/login">Login</a>
            @endif
        </div>
    </div>

    <div class="container">
        <h1>📅 Daftar Event UMKM Mahasiswa</h1>
        
        <div class="event-card">
            <div class="event-title">Festival Kuliner Mahasiswa</div>
            <div class="event-id">ID: 1</div>
            <div class="event-date">Tanggal: 2026-03-20</div>
            <a href="/events/1" class="btn-detail">Detail Event →</a>
        </div>

        <hr>

        <div class="event-card">
            <div class="event-title">Bazar Produk Kreatif</div>
            <div class="event-id">ID: 2</div>
            <div class="event-date">Tanggal: 2026-03-25</div>
            <a href="/events/2" class="btn-detail">Detail Event →</a>
        </div>

        <hr>

        <div class="event-card">
            <div class="event-title">Workshop Bisnis UMKM</div>
            <div class="event-id">ID: 3</div>
            <div class="event-date">Tanggal: 2026-04-01</div>
            <a href="/events/3" class="btn-detail">Detail Event →</a>
        </div>
    </div>
</body>
</html>