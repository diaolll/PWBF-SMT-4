<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papan Antrian</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background: #1E1B4B;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 24px;
        }

        .container { max-width: 760px; width: 100%; text-align: center; }

        h1 { color: #fff; font-size: 28px; font-weight: 700; letter-spacing: 2px; margin-bottom: 4px; }
        .subtitle { color: #A5B4FC; font-size: 15px; margin-bottom: 36px; }

        /* Main display */
        .display-box {
            border: 1px solid rgba(165, 180, 252, 0.25);
            border-radius: 16px;
            padding: 52px 40px;
        }

        .display-label { color: #A5B4FC; font-size: 16px; margin-bottom: 16px; }

        .number {
            font-size: clamp(80px, 20vw, 160px);
            font-weight: 700;
            color: #fff;
            line-height: 1;
            margin-bottom: 16px;
        }

        .name { font-size: 28px; color: #C4B5FD; font-weight: 600; min-height: 36px; }

        /* Sound button */
        .sound-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 28px;
            padding: 10px 22px;
            border: 1px solid rgba(165, 180, 252, 0.35);
            border-radius: 50px;
            background: transparent;
            color: #A5B4FC;
            font-size: 14px;
            cursor: pointer;
            transition: border-color .2s, color .2s;
        }
        .sound-btn:hover { border-color: #A5B4FC; color: #fff; }
        .sound-btn.active { border-color: #34D399; color: #34D399; }

        /* Waiting list */
        .waiting-box {
            border: 1px solid rgba(165, 180, 252, 0.25);
            border-radius: 16px;
            padding: 20px 24px;
            margin-top: 20px;
        }

        .waiting-title { color: #fff; font-size: 16px; margin-bottom: 14px; font-weight: 600; }
        .waiting-count { color: #A5B4FC; font-weight: 400; }

        .waiting-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            max-height: 120px;
            overflow-y: auto;
        }

        .waiting-item {
            border: 1px solid rgba(165, 180, 252, 0.3);
            border-radius: 20px;
            padding: 6px 14px;
            color: #C4B5FD;
            font-size: 13px;
        }

        .empty-text { color: #6B7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>PAPAN ANTRIAN</h1>
        <p class="subtitle">Silakan menunggu panggilan</p>

        <div class="display-box">
            <p class="display-label">Nomor Dipanggil</p>
            <div id="nomorDisplay" class="number">---</div>
            <div id="namaDisplay" class="name">Menunggu panggilan...</div>
        </div>

        <button id="soundToggle" onclick="toggleSound()" class="sound-btn">
            <i id="soundIcon" class="mdi mdi-volume-off" style="font-size: 20px;"></i>
            <span id="soundText">Aktifkan Suara</span>
        </button>

        <div class="waiting-box">
            <h2 class="waiting-title">
                Antrian Menunggu <span id="menungguCount" class="waiting-count">(0)</span>
            </h2>
            <div id="menungguList" class="waiting-list">
                <p class="empty-text">Tidak ada antrian menunggu</p>
            </div>
        </div>
    </div>

    <audio id="dingdongAudio" preload="auto">
        <source src="/audio/dingdong.mp3" type="audio/mpeg">
    </audio>

    <script>
        let soundEnabled = false;
        let lastDipanggil = null;
        const dingdongAudio = document.getElementById('dingdongAudio');
        const nomorDisplay = document.getElementById('nomorDisplay');
        const namaDisplay = document.getElementById('namaDisplay');
        const soundToggle = document.getElementById('soundToggle');
        const soundIcon = document.getElementById('soundIcon');
        const soundText = document.getElementById('soundText');

        function toggleSound() {
            soundEnabled = !soundEnabled;
            if (soundEnabled) {
                dingdongAudio.play().catch(() => {});
                soundIcon.className = 'mdi mdi-volume-high';
                soundText.textContent = 'Suara Aktif';
                soundToggle.classList.add('active');
            } else {
                soundIcon.className = 'mdi mdi-volume-off';
                soundText.textContent = 'Aktifkan Suara';
                soundToggle.classList.remove('active');
            }
        }

        function speak(nomor, nama) {
            if (!soundEnabled || !('speechSynthesis' in window)) return;
            const utterance = new SpeechSynthesisUtterance(
                `Nomor antrian, ${nomor}, atas nama, ${nama}, silakan menuju loket.`
            );
            utterance.lang = 'id-ID';
            utterance.rate = 0.9;
            window.speechSynthesis.cancel();
            window.speechSynthesis.speak(utterance);
        }

        function playDingdong() {
            if (!soundEnabled) return;
            dingdongAudio.currentTime = 0;
            dingdongAudio.play().catch(() => {});
        }

        function updateDisplay(data) {
            const dipanggil = data.dipanggil;

            if (dipanggil) {
                const nomorStr = String(dipanggil.nomor).padStart(3, '0');
                nomorDisplay.textContent = nomorStr;
                namaDisplay.textContent = dipanggil.nama;
                if (lastDipanggil !== dipanggil.nomor) {
                    lastDipanggil = dipanggil.nomor;
                    playDingdong();
                    // Tunggu 2 detik setelah dingdong, baru speak
                    setTimeout(() => speak(nomorStr, dipanggil.nama), 2000);
                }
            } else {
                nomorDisplay.textContent = '---';
                namaDisplay.textContent = 'Menunggu panggilan...';
                lastDipanggil = null;
            }

            const menungguList = document.getElementById('menungguList');
            const menungguCount = document.getElementById('menungguCount');
            const menunggu = data.menunggu || [];
            menungguCount.textContent = `(${menunggu.length})`;

            if (menunggu.length === 0) {
                menungguList.innerHTML = '<p class="empty-text">Tidak ada antrian menunggu</p>';
            } else {
                menungguList.innerHTML = menunggu.map(item =>
                    `<div class="waiting-item">${String(item.nomor).padStart(3, '0')} — ${item.nama}</div>`
                ).join('');
            }
        }

        async function fetchAntrianData() {
            try {
                const res = await fetch('/api/antrian');
                const data = await res.json();
                updateDisplay(data);
            } catch (err) {
                console.log('Poll error:', err);
            }
        }

        fetchAntrianData();
        const pollInterval = setInterval(fetchAntrianData, 1500);
        window.addEventListener('beforeunload', () => clearInterval(pollInterval));
    </script>
</body>
</html>