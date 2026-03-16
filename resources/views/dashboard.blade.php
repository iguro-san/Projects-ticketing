<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
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
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: white;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            text-align: center;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }
        .card-icon {
            font-size: 4em;
            margin-bottom: 20px;
        }
        .card h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.8em;
        }
        .card p {
            color: #666;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        .card a {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: background 0.3s;
        }
        .card a:hover {
            background: #764ba2;
        }
        .event-card a {
            background: #28a745;
        }
        .event-card a:hover {
            background: #218838;
        }
        .ticket-card a {
            background: #ffc107;
            color: #333;
        }
        .ticket-card a:hover {
            background: #e0a800;
        }
        .umkm-card a {
            background: #17a2b8;
        }
        .umkm-card a:hover {
            background: #138496;
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
        }
        .navbar-brand {
            font-size: 1.5em;
            font-weight: bold;
            color: #667eea;
        }
        .navbar-menu {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .navbar-menu a {
            color: #666;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .navbar-menu a:hover {
            background: #667eea;
            color: white;
        }
        .navbar-menu .logout {
            background: #dc3545;
            color: white;
        }
        .navbar-menu .logout:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Navbar -->
        <div class="navbar">
            <div class="navbar-brand">🎫 Admin Dashboard</div>
            <div class="navbar-menu">
                <a href="/dashboard">Dashboard</a>
                <a href="/events">Lihat Event</a>
                <a href="/login" class="logout">Logout</a>
            </div>
        </div>

        <h1>📊 Dashboard Admin</h1>
        
        <div class="dashboard-grid">
            <!-- Card Kelola Event -->
            <div class="card event-card">
                <div class="card-icon">📅</div>
                <h2>Kelola Event</h2>
                <p>Atur dan kelola semua event UMKM mahasiswa. Tambah, edit, atau hapus event.</p>
                <a href="/events">Kelola Event →</a>
            </div>

            <!-- Card Kelola Tiket -->
            <div class="card ticket-card">
                <div class="card-icon">🎫</div>
                <h2>Kelola Tiket</h2>
                <p>Monitor penjualan tiket dan kelola pemesanan tiket event.</p>
                <a href="/tickets">Kelola Tiket →</a>
            </div>

            <!-- Card Kelola UMKM -->
            <div class="card umkm-card">
                <div class="card-icon">🏪</div>
                <h2>Kelola UMKM</h2>
                <p>Atur data UMKM yang berpartisipasi dalam event.</p>
                <a href="#">Kelola UMKM →</a>
            </div>
        </div>

        <!-- Informasi Tambahan -->
        <div style="margin-top: 40px; background: white; border-radius: 15px; padding: 30px;">
            <h2 style="color: #333; margin-bottom: 20px;">📌 Ringkasan</h2>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; text-align: center;">
                <div>
                    <div style="font-size: 2em; font-weight: bold; color: #667eea;">3</div>
                    <div style="color: #666;">Total Event</div>
                </div>
                <div>
                    <div style="font-size: 2em; font-weight: bold; color: #28a745;">45</div>
                    <div style="color: #666;">Tiket Terjual</div>
                </div>
                <div>
                    <div style="font-size: 2em; font-weight: bold; color: #17a2b8;">12</div>
                    <div style="color: #666;">UMKM Aktif</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>