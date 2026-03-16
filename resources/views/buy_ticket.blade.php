<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelian Tiket</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            font-size: 2em;
            border-bottom: 3px solid #28a745;
            padding-bottom: 10px;
        }
        .event-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .event-info p {
            font-size: 1.2em;
            color: #333;
        }
        .event-info span {
            color: #28a745;
            font-weight: bold;
        }
        .btn-beli {
            background: #28a745;
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 1.2em;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        .btn-beli:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4);
        }
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-back:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎫 Pembelian Tiket</h1>
        
        <div class="event-info">
            <p>ID Event: <span>{{ $id }}</span></p>
        </div>
        
        <button class="btn-beli" onclick="alert('Tiket berhasil dibeli!')">
            Beli Tiket
        </button>
        
        <br>
        <a href="/events" class="btn-back">← Kembali ke Daftar Event</a>
    </div>
</body>
</html>