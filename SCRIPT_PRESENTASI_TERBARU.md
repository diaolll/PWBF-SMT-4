# SCRIPT PRESENTASI - UPDATE TERBARU
## Apa yang Baru Saya Tambahkan di Project PWBF SMT 4

---

## 📹 BAGIAN 1: OPENING (30 detik)

**[Visual: Slide Judul]**

Halo! Kali ini saya akan jelaskan **fitur-fitur terbaru** yang baru saya tambahkan ke project PWBF Semester 4. Ada **3 update utama** yang saya lakukan:

1. Modul **Customer** dengan fitur Kamera
2. Halaman **Sukses** untuk pembayaran
3. Fitur **Barcode** di label PDF barang

---

## 📹 BAGIAN 2: MODUL CUSTOMER - BARU! (3-4 menit) ⭐

**[Visual: Show file structure - CustomerController.php + views]**

Pertama, modul **Customer** yang saya buat dari nol. Modul ini untuk mengelola data customer dengan fitur unggulan: **upload foto via kamera**.

### Files Created:
```
app/Http/Controllers/CustomerController.php  (NEW)
resources/views/customer/index.blade.php     (NEW)
resources/views/customer/tambah1.blade.php   (NEW)
resources/views/customer/tambah2.blade.php   (NEW)
```

### Fitur Utama:

**1. CRUD Customer**
- List customer dengan pagination
- Tambah customer baru dengan foto
- Edit & delete customer

**2. Upload Foto via KAMERA** 📸

**[Demo: Buka /customer/tambah1 → klik area foto → kamera aktif]**

Di sini saya implementasikan akses kamera langsung dari browser menggunakan **MediaDevices API**.

**Teknis di Frontend (JavaScript):**
```javascript
// Mengakses kamera device
navigator.mediaDevices.getUserMedia({ video: { facingMode: facingMode } })
    .then(function(newStream) {
        stream = newStream;
        video.srcObject = stream;
    });

// Capture frame dari video ke canvas
canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

// Convert ke base64 untuk dikirim ke server
const dataUrl = canvas.toDataURL('image/png');
```

**Teknis di Backend (PHP - Laravel):**

Ada **2 metode penyimpanan** yang saya implementasikan:

**Method 1: Simpan sebagai BLOB (Database)**
```php
public function store1(Request $request) {
    // Decode base64 dari frontend
    $fotoData = base64_decode(
        preg_replace('/^data:image\/\w+;base64,/', '', $request->foto)
    );

    DB::table('customer')->insert([
        'nama'      => $request->nama,
        'foto_blob' => $fotoData,
        // ...
    ]);
}
```

**Method 2: Simpan sebagai File (Storage)**
```php
public function store2(Request $request) {
    $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $request->foto);
    $fileName = 'customer_' . time() . '.png';

    // Simpan ke storage/app/public/customers/
    Storage::disk('public')->put('customers/' . $fileName, base64_decode($imageData));

    DB::table('customer')->insert([
        'nama'      => $request->nama,
        'foto_path' => 'customers/' . $fileName,
        // ...
    ]);
}
```

**Kenapa 2 Method?**
| Method | Kelebihan | Kekurangan |
|--------|-----------|------------|
| BLOB | Cepat, semua di satu tempat | Database jadi besar |
| File | Database ringan, scalable | Perlu manage file |

**Fitur Ganti Kamera:**
```javascript
facingMode = (facingMode === 'user') ? 'environment' : 'user';
```
- `facingMode: 'user'` → Kamera depan (selfie)
- `facingMode: 'environment'` → Kamera belakang (foto orang lain)

**Integrasi Wilayah:**
Form customer juga terhubung dengan API Wilayah:
- Pilih Provinsi → AJAX load Kota
- Pilih Kota → AJAX load Kecamatan
- Pilih Kecamatan → AJAX load Kelurahan

**Routes:**
```php
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/',         [CustomerController::class, 'index']);
    Route::get('/tambah1',  [CustomerController::class, 'tambah1']);
    Route::post('/tambah1', [CustomerController::class, 'store1']);
    Route::get('/tambah2',  [CustomerController::class, 'tambah2']);
    Route::post('/tambah2', [CustomerController::class, 'store2']);
});
```

---

## 📹 BAGIAN 3: HALAMAN SUKSES PEMBAYARAN (1-2 menit)

**[Visual: kantin/sukses.blade.php]**

Update kedua adalah menambahkan **halaman sukses** untuk menampilkan detail setelah pembayaran.

### File Created:
```
resources/views/kantin/sukses.blade.php  (NEW)
```

### Sebelum vs Sesudah:

**Sebelum:**
Setelah pembayaran via Midtrans, user redirect tanpa informasi yang jelas.

**Sekarang:**
```php
Route::get('/kantin/sukses', [PaymentController::class, 'sukses']);
```

Di halaman sukses, user bisa lihat:
- **Order ID** - ID unik transaksi
- **Total Pembayaran** - Nominal yang dibayar
- **Status** - Status pembayaran (lunas/pending)
- **Detail Pesanan** - Item yang dibeli

**Teknis:**
```php
public function sukses(Request $request) {
    $orderId = $request->query('order_id');
    $pesanan = Pesanan::where('order_id', $orderId)->first();
    return view('kantin.sukses', compact('pesanan'));
}
```

---

## 📹 BAGIAN 4: BARCODE DI LABEL PDF (2 menit) 🆕

**[Visual: Show barang/pdf.blade.php + demo cetak label]**

Update ketiga adalah menambahkan **Barcode** di label harga barang.

### File Modified:
```
resources/views/barang/pdf.blade.php  (UPDATED)
app/Http/Controllers/BarangController.php  (UPDATED)
```

### Yang Ditambahkan:

**1. Barcode Generator di Controller**

Menggunakan library **Picqer Barcode Generator**:

```php
use Picqer\Barcode\BarcodeGeneratorPNG;

public function generatePDF(Request $request) {
    // ...

    // Generate barcode untuk setiap barang
    $generator = new BarcodeGeneratorPNG();
    $barcodes = [];
    foreach ($selectedBarang as $item) {
        // Generate barcode dalam format PNG, encode ke base64
        $barcodes[$item->id_barang] = base64_encode(
            $generator->getBarcode($item->id_barang, $generator::TYPE_CODE_128)
        );
    }

    return Pdf::loadView('barang.pdf', compact('selectedBarang', 'skipCount', 'barcodes'));
}
```

**2. Tampilan Barcode di PDF**

**[Show pdf.blade.php - barcode section]**

```html
<td>
    {{-- Barcode di atas nama --}}
    @if(isset($barcodes[$b->id_barang]))
        <div class="barcode">
            <img src="data:image/png;base64,{{ $barcodes[$b->id_barang] }}">
        </div>
    @endif

    <div class="nama">{{ $b->nama }}</div>
    <div class="harga">Rp {{ number_format($b->harga, 0, ',', '.') }}</div>
    <div class="id">{{ $b->id_barang }}</div>
</td>
```

**Layout Label (TnJ 108):**
- Ukuran kertas: A4
- Grid: 8 baris × 5 kolom = 40 label per halaman
- Setiap cell: 38mm × 18mm
- Urutan dari atas ke bawah:
  1. **Barcode** (CODE 128)
  2. **Nama Barang** (bold, uppercase)
  3. **Harga** (format Rp)
  4. **ID Barang** (kecil di bawah)

**Kenapa Barcode?**
- Scanning barang lebih cepat di kasir
- Mengurangi human error saat input
- Professional look untuk label harga

---

## 📹 BAGIAN 5: CLEANUP (30 detik)

**File yang dihapus:**
```
resources/views/barang/draf  (DELETED)
```

Ini adalah file draft/working file yang sudah tidak diperlukan, jadi saya hapus untuk menjaga kebersihan code.

---

## 📹 BAGIAN 6: RINGKASAN COMMIT (1 menit)

**[Show Git Summary]**

| File | Status | Deskripsi |
|------|--------|-----------|
| `CustomerController.php` | **NEW** | Controller customer (2 method storage) |
| `customer/index.blade.php` | **NEW** | List customer |
| `customer/tambah1.blade.php` | **NEW** | Form + kamera (BLOB) |
| `customer/tambah2.blade.php` | **NEW** | Form + kamera (File) |
| `kantin/sukses.blade.php` | **NEW** | Halaman sukses pembayaran |
| `barang/pdf.blade.php` | **MOD** | Tambah barcode di label |
| `BarangController.php` | **MOD** | Generate barcode PNG |
| `barang/draf` | **DEL** | Cleanup |

**Total:** 6 file baru, 2 file dimodifikasi, 1 file dihapus
**Lines:** +4,480 / -886

---

## 📹 BAGIAN 7: DEMO (2-3 menit)

**[Live Demo]**

"Sekarang saya tunjukkan demo fitur-fitur barunya:"

1. **Tambah Customer dengan Kamera**
   - Buka `/customer/tambah1`
   - Isi nama & alamat
   - Pilih wilayah (cascade dropdown)
   - Klik area foto → kamera aktif
   - Ganti kamera (depan/belakang)
   - Ambil foto → preview
   - Simpan → data tersimpan

2. **Cetak Label dengan Barcode**
   - Pilih barang di list
   - Set posisi awal (X, Y)
   - Generate PDF
   - Show barcode di atas nama barang

3. **Halaman Sukses Pembayaran**
   - Order via kantin
   - Bayar via Midtrans
   - Redirect ke halaman sukses

---

## 📹 BAGIAN 8: PENUTUP (30 detik)

"Jadi di update terbaru ini, saya fokus ke 3 hal:

1. **Modul Customer** - CRUD lengkap dengan fitur foto kamera (bisa ganti kamera depan/belakang)
2. **Halaman Sukses** - User experience lebih baik setelah pembayaran
3. **Barcode Label** - Label harga profesional dengan barcode untuk scanning

Terima kasih!"

---

## 🎯 KEY TERMS UNTUK EXPLAIN:

| Term | Penjelasan |
|------|------------|
| **MediaDevices API** | Browser API untuk akses kamera/microphone |
| **getUserMedia()** | Method untuk request akses kamera |
| **Base64** | Encoding binary ke text untuk kirim via HTTP |
| **BLOB** | Binary Large Object - simpan file di database |
| **Canvas** | Element HTML untuk draw/capture gambar |
| **Facing Mode** | Pilih kamera: `user` (depan) atau `environment` (belakang) |
| **Playsinline** | Attribute biar video jalan di iOS Safari |
| **CODE 128** | Tipe barcode yang bisa encode semua karakter ASCII |
| **Picqer** | Library PHP untuk generate barcode |
| **Axios** | HTTP client untuk AJAX (fetch tanpa reload) |

---

## 📋 ROUTES BARU:

```php
// Customer Routes
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/',         [CustomerController::class, 'index'])->name('index');
    Route::get('/tambah1',  [CustomerController::class, 'tambah1'])->name('tambah1');
    Route::post('/tambah1', [CustomerController::class, 'store1'])->name('store1');
    Route::get('/tambah2',  [CustomerController::class, 'tambah2'])->name('tambah2');
    Route::post('/tambah2', [CustomerController::class, 'store2'])->name('store2');
});

// Kantin Sukses Route
Route::get('/kantin/sukses', [PaymentController::class, 'sukses'])->name('kantin.sukses');
```

---

## 📋 API YANG DIPAKAI (MODUL CUSTOMER):

```php
// API Wilayah (cascade dropdown)
GET /api/kota/{id_provinsi}      → Get kota by provinsi
GET /api/kecamatan/{id_kota}     → Get kecamatan by kota
GET /api/kelurahan/{id_kecamatan} → Get kelurahan by kecamatan
```

---

*Durasi presentasi: ~8-10 menit*