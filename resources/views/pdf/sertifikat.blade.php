<!DOCTYPE html>
<html>
<head>
    <title>Sertifikat</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', sans-serif; text-align: center; border: 20px solid #7a43df; height: 94%; padding: 50px; }
        .title { font-size: 60px; font-weight: bold; color: #7a43df; margin-top: 50px; }
        .subtitle { font-size: 25px; margin-top: 10px; color: #555; }
        .name { font-size: 45px; font-weight: bold; color: #000; margin: 40px 0; border-bottom: 2px solid #7a43df; display: inline-block; padding: 0 30px; }
        .content { font-size: 20px; line-height: 1.5; color: #333; }
        .footer { margin-top: 100px; font-size: 18px; }
    </style>
</head>
<body>
    <div class="title">SERTIFIKAT</div>
    <div class="subtitle">Diberikan Sebagai Penghargaan Kepada:</div>
    <div class="name">{{ $nama }}</div>
    <div class="content">
        Telah berhasil menyelesaikan pelatihan sistem informasi<br>
        <strong>Dashboard Purple Admin Edition 2026</strong>
    </div>
    <div class="footer">Diterbitkan pada: {{ $tanggal }}</div>
</body>
</html>