<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Event</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2em;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        .info-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            margin-bottom: 25px;
        }
        .event-id {
            font-size: 1.2em;
            color: #667eea;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .event-id span {
            color: #333;
            font-weight: normal;
        }
        .btn-beli {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: background 0.3s;
        }
        .btn-beli:hover {
            background: #218838;
        }
        .btn-back {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
        }
        .btn-back:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Detail Event</h1>
        
        <div class="info-card">
            <div class="event-id">ID Event: <span>{{ $id }}</span></div>
        </div>
        
        <div style="text-align: center;">
            <a href="/tickets/buy/{{ $id }}" class="btn-beli">🎫 Beli Tiket</a>
        </div>
        
        <a href="/events" class="btn-back">← Kembali ke Daftar Event</a>
    </div>
</body>
</html>