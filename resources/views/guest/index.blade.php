<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Nomor Antrian</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            background: #F3F4F6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 36px 32px;
            width: 100%;
            max-width: 380px;
        }
        h1 { font-size: 20px; color: #111827; margin-bottom: 4px; }
        .sub { font-size: 14px; color: #6B7280; margin-bottom: 24px; }
        .error { background: #FEE2E2; color: #991B1B; font-size: 13px; padding: 10px 12px; border-radius: 6px; margin-bottom: 16px; border: 1px solid #FCA5A5; }
        label { display: block; font-size: 13px; font-weight: bold; color: #374151; margin-bottom: 6px; }
        input[type="text"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #D1D5DB;
            border-radius: 6px;
            font-size: 15px;
            outline: none;
        }
        input[type="text"]:focus { border-color: #4F46E5; }
        button {
            width: 100%;
            margin-top: 14px;
            padding: 11px;
            background: #4F46E5;
            color: #fff;
            font-size: 15px;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover { background: #3730A3; }
        .link { text-align: center; margin-top: 16px; font-size: 13px; }
        .link a { color: #4F46E5; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Ambil Nomor Antrian</h1>
        <p class="sub">Masukkan nama Anda untuk mendapatkan nomor antrian.</p>

        @if ($errors->has('nama'))
            <div class="error">{{ $errors->first('nama') }}</div>
        @endif

        <form action="{{ route('guest.daftar') }}" method="POST">
            @csrf
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" required autofocus
                value="{{ old('nama') }}" placeholder="Contoh: Budi Santoso">
            <button type="submit">Ambil Antrian</button>
        </form>

        <div class="link">
            <a href="{{ url('/papan') }}">Lihat Papan Antrian &rarr;</a>
        </div>
    </div>
</body>
</html>