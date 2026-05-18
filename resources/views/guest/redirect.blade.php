<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memproses...</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            background: #F3F4F6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .wrap { text-align: center; }
        .spinner {
            width: 48px; height: 48px;
            border: 3px solid #E5E7EB;
            border-top-color: #4F46E5;
            border-radius: 50%;
            animation: spin .8s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        h1 { font-size: 18px; color: #111827; margin-bottom: 6px; }
        p { font-size: 14px; color: #6B7280; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="spinner"></div>
        <h1>Memproses Antrian…</h1>
        <p>Mohon tunggu sebentar</p>
    </div>
    <script>
        setTimeout(() => {
            window.location.href = '{{ route('guest.tiket', ['nomor' => $nomor, 'nama' => $nama]) }}';
        }, 1500);
    </script>
</body>
</html>