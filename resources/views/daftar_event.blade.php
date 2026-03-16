<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Daftar Event</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <div class="container mt-5">
    <a href="/login" class="btn btn-outline-primary">Login</a>
    <h1 class="text-center mb-4">Daftar Event</h1>

    <div class="row">
      @foreach($events as $event)
        <div class="col-md-4">
          <div class="card shadow-sm mb-4">
            <div class="card-body">
              <h5 class="card-title">{{ $event['nama'] }}</h5>
              <p class="card-text">
                <strong>ID:</strong> {{ $event['id'] }} <br>
                <strong>Tanggal:</strong> {{ $event['tanggal'] }}
              </p>
              <a href="/daftarevent/{{ $event['id'] }}" class="btn btn-primary btn-sm">
                Detail Event →
              </a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

</body>
</html>
