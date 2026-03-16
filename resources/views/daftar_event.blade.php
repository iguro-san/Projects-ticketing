<html>
  <body>
    <h1>Daftar Event</h1>
    <ul>
      @foreach($events as $event)
        <li>
          {{ $event['id'] }} - {{ $event['nama'] }} ({{ $event['tanggal'] }})
        </li>
      @endforeach
    </ul>
    <a href="detail_event.blade.php">Detail Event => </a>
  </body>
</html>
