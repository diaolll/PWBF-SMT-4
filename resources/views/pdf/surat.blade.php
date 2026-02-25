<!DOCTYPE html>
<html>
<head>
    <title>Surat Undangan</title>
    <style>
        body { font-family: 'Times New Roman', serif; margin: 30px; line-height: 1.6; }
        .kop-surat { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 30px; }
        .kop-surat h2 { margin: 0; padding: 0; font-size: 24px; }
        .isi-surat { text-align: justify; }
        .ttd { float: right; margin-top: 50px; text-align: center; width: 250px; }
    </style>
</head>
<body>
    <div class="kop-surat">
        <h2>UNIVERSITAS TEKNOLOGI PURPLE 2</h2>
        <p>Jl. Jenderal Sudirman No. 45, Jakarta Selatan<br>Telp: (021) 123456 | Website: www.purple-univ.ac.id</p>
    </div>
    <div class="isi-surat">
        <p>Jakarta, {{ $tanggal }}</p>
        <p>Nomor: {{ $nomor_surat }}<br>Perihal: {{ $perihal }}</p>
        <p>Yth. Seluruh Staff Pengajar<br>Fakultas Ilmu Komputer</p>
        <p>Dengan hormat,</p>
        <p>Sehubungan dengan pengembangan sistem dashboard baru, kami mengundang Bapak/Ibu untuk hadir pada sesi demonstrasi sistem yang akan dilaksanakan minggu depan.</p>
        <p>Demikian surat ini kami sampaikan, atas perhatiannya kami ucapkan terima kasih.</p>
    </div>
    <div class="ttd">
        Hormat kami,<br>Dekan Fakultas<br><br><br><br>
        <strong>( Prof. David Greymaax )</strong>
    </div>
</body>
</html>