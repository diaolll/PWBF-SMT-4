<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Antrian #{{ $antrian->nomor }}</title>
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
            padding: 40px 32px;
            width: 100%;
            max-width: 340px;
            text-align: center;
        }
        .label { font-size: 13px; color: #6B7280; margin-bottom: 4px; }
        .number { font-size: 80px; font-weight: bold; color: #4F46E5; line-height: 1; margin-bottom: 10px; }
        .name { font-size: 18px; font-weight: bold; color: #111827; margin-bottom: 24px; }
        .status-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #F3F4F6;
            border-bottom: 1px solid #F3F4F6;
            padding: 12px 0;
            margin-bottom: 16px;
        }
        .status-label { font-size: 13px; color: #6B7280; }
        .badge {
            font-size: 12px;
            font-weight: bold;
            padding: 4px 12px;
            border-radius: 20px;
        }
        .badge-waiting { background: #FEF3C7; color: #92400E; }
        .badge-called  { background: #D1FAE5; color: #065F46; }
        .alert {
            display: none;
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            border-radius: 6px;
            padding: 12px 14px;
            text-align: left;
            margin-bottom: 16px;
        }
        .alert.show { display: block; }
        .alert strong { font-size: 14px; color: #15803D; display: block; margin-bottom: 2px; }
        .alert span { font-size: 13px; color: #166534; }
        .info { font-size: 12px; color: #9CA3AF; margin-top: 8px; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="card">
        <p class="label">Nomor Antrian Anda</p>
        <div class="number">{{ str_pad($antrian->nomor, 3, '0', STR_PAD_LEFT) }}</div>
        <p class="name">{{ $antrian->nama }}</p>

        <div class="status-row">
            <span class="status-label">Status</span>
            <span id="statusBadge" class="badge badge-waiting">Menunggu</span>
        </div>

        <div id="dipanggilAlert" class="alert">
            <strong>Nomor Anda Dipanggil!</strong>
            <span>Silakan menuju loket sekarang.</span>
        </div>

        <p class="info">Jangan tutup halaman ini.<br>Anda akan mendapat notifikasi saat dipanggil.</p>
    </div>

    <script>
        const nomorAntrian = {{ $antrian->nomor }};
        const statusBadge = document.getElementById('statusBadge');
        const dipanggilAlert = document.getElementById('dipanggilAlert');

        function updateStatus(data) {
            const dipanggil = data.dipanggil;
            if (dipanggil && dipanggil.nomor === nomorAntrian) {
                statusBadge.textContent = 'Dipanggil';
                statusBadge.className = 'badge badge-called';
                dipanggilAlert.classList.add('show');
                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification('Antrian Dipanggil!', {
                        body: `Nomor ${String(nomorAntrian).padStart(3, '0')} sedang dipanggil.`,
                        icon: '/favicon.ico'
                    });
                }
            } else if (dipanggil) {
                statusBadge.textContent = 'Melayani ' + String(dipanggil.nomor).padStart(3, '0');
                statusBadge.className = 'badge badge-waiting';
            } else {
                statusBadge.textContent = 'Menunggu';
                statusBadge.className = 'badge badge-waiting';
            }
        }

        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }

        async function fetchAntrianData() {
            try {
                const res = await fetch('/api/antrian');
                const data = await res.json();
                updateStatus(data);
            } catch (err) {}
        }

        fetchAntrianData();
        const pollInterval = setInterval(fetchAntrianData, 2000);
        window.addEventListener('beforeunload', () => clearInterval(pollInterval));
    </script>
</body>
</html>