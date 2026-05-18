# Laporan Modul Antrian Real-Time

**Tanggal:** 2026-05-17
**Versi Laravel:** 12.51.0
**PHP:** 8.4.11

---

## 📋 Ringkasan

Modul antrian real-time menggunakan **Polling API** (bukan SSE) untuk menghindari blocking pada PHP server. Sistem ini memungkinkan:
- Guest mengambil nomor antrian
- Admin memanggil dan mengelola antrian
- Papan antrian menampilkan nomor yang sedang dipanggil
- Notifikasi real-time ke semua connected clients

---

## 🗂️ File Baru yang Dibuat

### 1. Middleware
**File:** `app/Http/Middleware/RoleMiddleware.php`

Memproteksi route berdasarkan role user di database.

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->role !== $role) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
```

**Fungsi:** Mengecek apakah user login dan memiliki role yang sesuai sebelum mengakses route.

---

### 2. Models

#### **File:** `app/Models/Antrian.php`

Model untuk tabel antrian.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    protected $table = 'antrian';

    protected $fillable = [
        'nomor',
        'nama',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
```

**Fungsi:** Representasi data antrian dari tabel `antrian`.

---

### 3. Controllers

#### **File:** `app/Http/Controllers/AntrianAdminController.php`

Controller untuk admin mengelola antrian.

```php
<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AntrianAdminController extends Controller
{
    public function index()
    {
        return view('admin.antrian');
    }

    public function tambah(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:255']);

        $lastNomor = Antrian::max('nomor') ?? 0;
        $nomor = $lastNomor + 1;

        $antrian = Antrian::create([
            'nomor' => $nomor,
            'nama' => $request->nama,
            'status' => 'menunggu',
        ]);

        $this->updateCache();

        return redirect()->back()->with('success', "Antrian #$nomor untuk {$request->nama} berhasil ditambahkan.");
    }

    public function panggil()
    {
        $menunggu = Antrian::where('status', 'menunggu')->orderBy('nomor')->first();

        if (!$menunggu) {
            return redirect()->back()->with('error', 'Tidak ada antrian menunggu.');
        }

        $menunggu->update(['status' => 'dipanggil']);
        $this->updateCache();

        return redirect()->back()->with('success', "Memanggil antrian #{$menungai->nomor} - {$menunggu->nama}");
    }

    public function tandaiTerlambat($id)
    {
        $antrian = Antrian::findOrFail($id);
        $antrian->update(['status' => 'terlambat']);
        $this->updateCache();

        return redirect()->back()->with('success', "Antrian #{$antrian->nomor} ditandai terlambat.");
    }

    public function panggilTerlambat($id)
    {
        $antrian = Antrian::findOrFail($id);

        $sedangDipanggil = Antrian::where('status', 'dipanggil')->first();
        if ($sedangDipanggil) {
            $sedangDipanggil->update(['status' => 'selesai']);
        }

        $antrian->update(['status' => 'dipanggil']);
        $this->updateCache();

        return redirect()->back()->with('success', "Memanggil antrian terlambat #{$antrian->nomor} - {$antrian->nama}");
    }

    public function reset()
    {
        Antrian::truncate();
        Cache::forget('antrian_data');

        return redirect()->back()->with('success', 'Semua data antrian berhasil direset.');
    }

    private function updateCache()
    {
        $menunggu = Antrian::where('status', 'menunggu')->orderBy('nomor')->get()->toArray();
        $dipanggil = Antrian::where('status', 'dipanggil')->first();
        $terlambat = Antrian::where('status', 'terlambat')->orderBy('nomor')->get()->toArray();

        Cache::put('antrian_data', [
            'menunggu' => $menunggu,
            'dipanggil' => $dipanggil ? $dipanggil->toArray() : null,
            'terlambat' => $terlambat,
        ], now()->addHours(24));
    }
}
```

**Fungsi:**
| Method | Fungsi |
|--------|--------|
| `index()` | Tampilkan dashboard admin |
| `tambah()` | Tambah antrian manual (input nama) |
| `panggil()` | Panggil nomor antrian berikutnya |
| `tandaiTerlambat()` | Tandai antrian sebagai terlambat |
| `panggilTerlambat()` | Panggil ulang antrian yang terlambat |
| `reset()` | Hapus semua data antrian |
| `updateCache()` | Update cache untuk real-time |

---

#### **File:** `app/Http/Controllers/AntrianGuestController.php`

Controller untuk guest mengambil tiket antrian.

```php
<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AntrianGuestController extends Controller
{
    public function index()
    {
        return view('guest.index');
    }

    public function daftar(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:255']);

        $lastNomor = Antrian::max('nomor') ?? 0;
        $nomor = $lastNomor + 1;

        $antrian = Antrian::create([
            'nomor' => $nomor,
            'nama' => $request->nama,
            'status' => 'menunggu',
        ]);

        $this->updateCache();

        return redirect()->route('guest.redirect', [
            'nomor' => $antrian->nomor,
            'nama' => $antrian->nama,
        ]);
    }

    public function redirectView($nomor, $nama)
    {
        return view('guest.redirect', compact('nomor', 'nama'));
    }

    public function tiket($nomor, $nama)
    {
        $antrian = Antrian::where('nomor', $nomor)->where('nama', $nama)->firstOrFail();

        return view('guest.tiket', compact('antrian'));
    }

    private function updateCache()
    {
        $menunggu = Antrian::where('status', 'menunggu')->orderBy('nomor')->get()->toArray();
        $dipanggil = Antrian::where('status', 'dipanggil')->first();
        $terlambat = Antrian::where('status', 'terlambat')->orderBy('nomor')->get()->toArray();

        Cache::put('antrian_data', [
            'menunggu' => $menunggu,
            'dipanggil' => $dipanggil ? $dipanggil->toArray() : null,
            'terlambat' => $terlambat,
        ], now()->addHours(24));
    }
}
```

**Fungsi:**
| Method | Fungsi |
|--------|--------|
| `index()` | Tampilkan form input nama |
| `daftar()` | Simpan antrian baru + auto increment nomor |
| `redirectView()` | Redirect page sebelum buka tiket |
| `tiket()` | Tampilkan tiket antrian dengan real-time update |

---

#### **File:** `app/Http/Controllers/AntrianPapanController.php`

Controller untuk papan antrian (display).

```php
<?php

namespace App\Http\Controllers;

class AntrianPapanController extends Controller
{
    public function index()
    {
        return view('papan.index');
    }
}
```

**Fungsi:** Tampilkan halaman papan antrian dengan real-time update.

---

#### **File:** `app/Http/Controllers/AntrianSSEController.php`

Controller untuk API polling dan SSE (fallback).

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class AntrianSSEController extends Controller
{
    // Polling endpoint - lighter alternative to SSE
    public function poll()
    {
        $data = Cache::get('antrian_data', [
            'menunggu' => [],
            'dipanggil' => null,
            'terlambat' => [],
        ]);

        return response()->json($data);
    }

    public function stream()
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        set_time_limit(0);
        ignore_user_abort(true);

        // Send initial data immediately
        $data = Cache::get('antrian_data', [
            'menunggu' => [],
            'dipanggil' => null,
            'terlambat' => [],
        ]);
        echo "data: " . json_encode($data) . "\n\n";
        ob_flush();
        flush();

        $lastData = json_encode($data);
        $iterations = 0;
        $maxIterations = 300; // 5 minutes max
        $emptyIterations = 0;
        $sleepTime = 2;

        while ($iterations < $maxIterations) {
            if (connection_aborted()) {
                break;
            }

            $data = Cache::get('antrian_data', [
                'menunggu' => [],
                'dipanggil' => null,
                'terlambat' => [],
            ]);

            $jsonData = json_encode($data);

            if ($jsonData !== $lastData) {
                echo "data: $jsonData\n\n";
                $lastData = $jsonData;
                $emptyIterations = 0;
                $sleepTime = 2;
            } else {
                $emptyIterations++;
                if ($emptyIterations > 7) {
                    $sleepTime = 3;
                }
            }

            echo ": ping\n\n";

            ob_flush();
            flush();
            sleep($sleepTime);
            $iterations++;
        }

        echo "event: close\ndata: Connection timeout, please refresh\n\n";
        exit;
    }
}
```

**Fungsi:**
| Method | Fungsi |
|--------|--------|
| `poll()` | **API** - Return JSON data dari cache (digunakan untuk polling) |
| `stream()` | **SSE** - Server-Sent Events untuk real-time (fallback) |

---

### 4. Views

#### **File:** `resources/views/guest/index.blade.php`

Halaman form input nama untuk mengambil antrian.

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Nomor Antrian</title>
</head>
<body>
    <div style="max-width: 400px; margin: 50px auto; padding: 30px; border: 1px solid #ddd; border-radius: 8px; font-family: Arial, sans-serif;">
        <h1 style="text-align: center; color: #333;">Ambil Nomor Antrian</h1>
        <p style="text-align: center; color: #666;">Silakan masukkan nama Anda</p>

        <form action="{{ route('guest.daftar') }}" method="POST" style="margin-top: 20px;">
            @csrf
            @if ($errors->has('nama'))
                <p style="color: red; margin-bottom: 10px;">{{ $errors->first('nama') }}</p>
            @endif

            <div style="margin-bottom: 15px;">
                <label for="nama" style="display: block; margin-bottom: 5px; font-weight: bold;">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" required
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"
                       placeholder="Contoh: Budi Santoso" autofocus>
            </div>

            <button type="submit"
                    style="width: 100%; padding: 12px; background: #4F46E5; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">
                Ambil Antrian
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ url('/papan') }}" style="color: #4F46E5; text-decoration: none;">Lihat Papan Antrian &rarr;</a>
        </div>
    </div>
</body>
</html>
```

**Fungsi:** Form input nama untuk guest mengambil nomor antrian.

---

#### **File:** `resources/views/guest/redirect.blade.php`

Halaman redirect sementara sebelum menuju tiket.

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memproses...</title>
    <style>
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #10B981;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body style="display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; font-family: Arial, sans-serif; background: #F0FDF4;">
    <div style="text-align: center;">
        <div class="spinner"></div>
        <h1 style="color: #333; margin-top: 20px;">Memproses Antrian...</h1>
        <p style="color: #666;">Mohon tunggu sebentar</p>
    </div>

    <script>
        const nomor = '{{ $nomor }}';
        const nama = '{{ $nama }}';
        const tiketUrl = '{{ route('guest.tiket', ['nomor' => $nomor, 'nama' => $nama]) }}';

        setTimeout(() => {
            window.location.href = tiketUrl;
        }, 1500);

        setTimeout(() => {
            window.open(tiketUrl, '_blank');
        }, 2000);
    </script>
</body>
</html>
```

**Fungsi:** Halaman loading sebelum redirect ke tiket + membuka tab baru.

---

#### **File:** `resources/views/guest/tiket.blade.php`

Halaman tiket antrian dengan real-time update (polling).

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Antrian #{{ $antrian->nomor }}</title>
    <!-- CSS styles disini -->
</head>
<body>
    <div class="ticket">
        <p class="display-label">Nomor Antrian Anda</p>
        <div class="number">{{ str_pad($antrian->nomor, 3, '0', STR_PAD_LEFT) }}</div>
        <p class="name">{{ $antrian->nama }}</p>

        <div class="status-box">
            <span>Status</span>
            <span id="statusBadge" class="status-badge status-menunggu">Menunggu</span>
        </div>

        <div id="dipanggilAlert" class="alert-box">
            <!-- Alert saat dipanggil -->
        </div>
    </div>

    <script>
        const nomorAntrian = {{ $antrian->nomor }};

        function updateStatus(data) {
            const dipanggil = data.dipanggil;

            if (dipanggil && dipanggil.nomor === nomorAntrian) {
                // Tampilkan alert dipanggil
            }
        }

        // POLLING - fetch data setiap 2 detik
        async function fetchAntrianData() {
            try {
                const res = await fetch('/api/antrian');
                const data = await res.json();
                updateStatus(data);
            } catch (err) {
                console.log('Poll error:', err);
            }
        }

        fetchAntrianData();
        pollInterval = setInterval(fetchAntrianData, 2000);
    </script>
</body>
</html>
```

**Fungsi:** Tiket antrian dengan update status real-time via polling setiap 2 detik.

---

#### **File:** `resources/views/admin/antrian.blade.php`

Dashboard admin untuk mengelola antrian.

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Antrian</title>
    <!-- CSS styles -->
</head>
<body>
    <nav>
        <h1>Dashboard Antrian</h1>
        <a href="{{ route('papan.index') }}" target="_blank">Buka Papan &rarr;</a>
    </nav>

    <div class="container">
        <!-- Form tambah antrian manual -->
        <div class="card">
            <form action="{{ route('antrian.tambah') }}" method="POST">
                @csrf
                <input type="text" name="nama" placeholder="Nama pelanggan">
                <button type="submit">Tambah</button>
            </form>
        </div>

        <!-- Sedang dipanggil -->
        <div class="card">
            <h2>Sedang Dipanggil</h2>
            <div id="dipanggilDisplay">...</div>
        </div>

        <!-- Tombol aksi -->
        <div class="card">
            <form action="{{ route('antrian.panggil') }}" method="POST">
                @csrf
                <button>Panggil Berikutnya</button>
            </form>
            <form action="{{ route('antrian.reset') }}" method="POST">
                @csrf
                <button>Reset</button>
            </form>
        </div>

        <!-- List menunggu & terlambat -->
        <div id="menungguList"></div>
        <div id="terlambatList"></div>
    </div>

    <script>
        // POLLING - fetch data setiap 1.5 detik
        async function fetchAntrianData() {
            const res = await fetch('/api/antrian');
            const data = await res.json();
            renderMenunggu(data.menunggu);
            renderTerlambat(data.terlambat);
            renderDipanggil(data.dipanggil);
        }

        fetchAntrianData();
        pollInterval = setInterval(fetchAntrianData, 1500);
    </script>
</body>
</html>
```

**Fungsi:** Dashboard admin dengan:
- Form tambah antrian manual
- Tombol panggil berikutnya
- List antrian menunggu (dengan tombol tandai terlambat)
- List antrian terlambat (double click untuk panggil)
- Tombol reset semua data

---

#### **File:** `resources/views/papan/index.blade.php`

Papan antrian untuk display besar.

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Papan Antrian</title>
    <!-- CSS gradient background -->
</head>
<body>
    <div class="container">
        <h1>PAPAN ANTRIAN</h1>

        <div class="display-box">
            <p class="display-label">Nomor Dipanggil</p>
            <div id="nomorDisplay" class="number">---</div>
            <div id="namaDisplay" class="name">Menunggu panggilan...</div>
        </div>

        <button id="soundToggle" onclick="toggleSound()">
            Aktifkan Suara
        </button>

        <div class="waiting-box">
            <h2>Antrian Menunggu (<span id="menungguCount">0</span>)</h2>
            <div id="menungguList"></div>
        </div>
    </div>

    <audio id="dingdongAudio" preload="auto">
        <source src="/audio/dingdong.mp3" type="audio/mpeg">
    </audio>

    <script>
        let soundEnabled = false;

        function toggleSound() {
            soundEnabled = !soundEnabled;
            // Update UI
        }

        function speak(nomor, nama) {
            if (!soundEnabled) return;
            const text = `Nomor antrian, ${nomor}, atas nama, ${nama}, silakan menuju loket.`;
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            window.speechSynthesis.speak(utterance);
        }

        // POLLING
        async function fetchAntrianData() {
            const res = await fetch('/api/antrian');
            const data = await res.json();
            // Update display
        }

        fetchAntrianData();
        pollInterval = setInterval(fetchAntrianData, 1500);
    </script>
</body>
</html>
```

**Fungsi:** Papan antrian dengan:
- Display nomor & nama yang sedang dipanggil
- Tombol aktifkan suara (dingdong + Web Speech API)
- List antrian menunggu

---

## 🔧 File yang Dimodifikasi

### 1. **File:** `app/Models/User.php`

Menambahkan kolom `role` ke fillable dan method `isAdmin()`.

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'id_google',
    'otp',
    'role',      // NEW
];

public function isAdmin(): bool
{
    return $this->role === 'admin';
}
```

---

### 2. **File:** `bootstrap/app.php`

Mendaftarkan middleware alias.

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->validateCsrfTokens(except: [
        'midtrans/callback',
        '/midtrans/callback',
        'midtrans/*',
    ]);

    $middleware->trustProxies(at: '*');

    // NEW - Register role middleware
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

---

### 3. **File:** `routes/web.php`

Menambahkan route untuk sistem antrian.

```php
use App\Http\Controllers\AntrianGuestController;
use App\Http\Controllers\AntrianPapanController;
use App\Http\Controllers\AntrianAdminController;
use App\Http\Controllers\AntrianSSEController;

// Guest routes (public)
Route::prefix('guest')->name('guest.')->group(function () {
    Route::get('/', [AntrianGuestController::class, 'index'])->name('index');
    Route::post('/daftar', [AntrianGuestController::class, 'daftar'])->name('daftar');
    Route::get('/redirect/{nomor}/{nama}', [AntrianGuestController::class, 'redirectView'])->name('redirect');
    Route::get('/tiket/{nomor}/{nama}', [AntrianGuestController::class, 'tiket'])->name('tiket');
});

// Papan antrian (public)
Route::get('/papan', [AntrianPapanController::class, 'index'])->name('papan.index');

// API polling endpoint (public)
Route::get('/api/antrian', [AntrianSSEController::class, 'poll'])->name('api.antrian');

// SSE endpoint (public) - fallback
Route::get('/sse/antrian', [AntrianSSEController::class, 'stream'])->name('sse.antrian');

// Admin routes (auth + role:admin)
Route::middleware(['auth', 'role:admin'])->prefix('antrian')->name('antrian.')->group(function () {
    Route::get('/admin', [AntrianAdminController::class, 'index'])->name('admin');
    Route::post('/tambah', [AntrianAdminController::class, 'tambah'])->name('tambah');
    Route::post('/panggil', [AntrianAdminController::class, 'panggil'])->name('panggil');
    Route::post('/terlambat/{id}', [AntrianAdminController::class, 'tandaiTerlambat'])->name('terlambat');
    Route::post('/panggil-terlambat/{id}', [AntrianAdminController::class, 'panggilTerlambat'])->name('panggil-terlambat');
    Route::post('/reset', [AntrianAdminController::class, 'reset'])->name('reset');
});
```

---

### 4. **File:** `resources/views/layouts/Template.blade.php`

Menambahkan menu Modul 8 di sidebar.

```php
{{-- Modul 8 - Sistem Antrian --}}
<li class="nav-item">
    <a class="nav-link" data-bs-toggle="collapse" href="#ui-modul8">
        <span class="menu-title">Modul 8</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-bell-ring menu-icon"></i>
    </a>
    <div class="collapse" id="ui-modul8">
        <ul class="nav flex-column sub-menu">
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/antrian/admin') }}" target="_blank">
                    Admin Antrian
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/guest') }}" target="_blank">
                    Ambil Tiket (Guest)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/papan') }}" target="_blank">
                    Papan Antrian
                </a>
            </li>
        </ul>
    </div>
</li>
```

---

## 📊 Database Schema

### Tabel `users` (ditambah kolom)

```sql
ALTER TABLE users
ADD COLUMN role ENUM('admin', 'guest', 'papan') DEFAULT 'guest' AFTER updated_at;

UPDATE users SET role = 'admin' WHERE email = 'emailkamu@gmail.com';
```

### Tabel `antrian` (baru)

```sql
CREATE TABLE antrian (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nomor INT NOT NULL,
    nama VARCHAR(255) NOT NULL,
    status ENUM('menunggu', 'dipanggil', 'terlambat', 'selesai') DEFAULT 'menunggu',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

## 🚀 Routes List

| URL | Method | Access | Fungsi |
|-----|--------|--------|--------|
| `/guest` | GET | Public | Form ambil tiket |
| `/guest/daftar` | POST | Public | Simpan antrian |
| `/guest/tiket/{nomor}/{nama}` | GET | Public | Lihat tiket |
| `/papan` | GET | Public | Papan antrian |
| `/api/antrian` | GET | Public | API data (polling) |
| `/antrian/admin` | GET | Admin | Dashboard admin |
| `/antrian/tambah` | POST | Admin | Tambah antrian |
| `/antrian/panggil` | POST | Admin | Panggil berikutnya |
| `/antrian/terlambat/{id}` | POST | Admin | Tandai terlambat |
| `/antrian/panggil-terlambat/{id}` | POST | Admin | Panggil terlambat |
| `/antrian/reset` | POST | Admin | Reset data |

---

## 🔄 Alur Kerja

```
┌─────────────────────────────────────────────────────────────────┐
│                         ALUR ANTRIAN                             │
└─────────────────────────────────────────────────────────────────┘

1. GUEST mengambil tiket
   └─> /guest → input nama → simpan ke DB → update cache

2. ADMIN memanggil antrian
   └─> /antrian/admin → klik "Panggil" → update status → update cache

3. PAPAN menampilkan nomor
   └─> Polling /api/antrian → ambil dari cache → update display

4. GUEST cek status tiket
   └─> Polling /api/antrian → cek status → notifikasi jika dipanggil

Cache Structure:
{
    "menunggu": [{id, nomor, nama, status}, ...],
    "dipanggil": {id, nomor, nama, status} | null,
    "terlambat": [{id, nomor, nama, status}, ...]
}
```

---

## 🎵 Fitur Tambahan

### 1. Web Speech API (Papan Antrian)
Browser bisa membacakan nomor & nama antrian secara otomatis.

```javascript
const utterance = new SpeechSynthesisUtterance(
    `Nomor antrian, ${nomor}, atas nama, ${nama}, silakan menuju loket.`
);
utterance.lang = 'id-ID';
window.speechSynthesis.speak(utterance);
```

### 2. Browser Notification (Tiket Guest)
Notifikasi desktop saat nomor dipanggil.

```javascript
if ('Notification' in window && Notification.permission === 'granted') {
    new Notification('Antrian Dipanggil!', {
        body: `Nomor ${nomor} sedang dipanggil.`
    });
}
```

---

## ⚡ Performa & Optimasi

| Aspek | Implementasi |
|-------|--------------|
| **Real-time method** | Polling (bukan SSE) - menghindari blocking PHP |
| **Cache** | File cache - menyimpan data antrian untuk fast access |
| **Polling interval** | 1.5-2 detik - balance antara real-time & server load |
| **DB Query** | Hanya saat ada perubahan (tambah/panggil/terlambat) |
| **Non-blocking** | API request cepat, gak pegang connection |

---

## 📝 Catatan Penting

1. **Audio dingdong.mp3** perlu disediakan manual di `public/audio/dingdong.mp3`
2. **User harus punya role='admin'** untuk akses halaman admin
3. **Polling lebih stabil** untuk PHP development environment (php artisan serve)
4. Untuk production, pertimbangkan gunakan **Redis** untuk cache dan **WebSocket** untuk real-time

---

## 🐛 Known Issues & Solutions

| Issue | Solusi |
|-------|--------|
| Page loading terus | SSE blocking → ganti ke Polling |
| Lemot buka banyak tab | php artisan serve single-threaded → gunakan polling |
| Suara tidak keluar | Browser perlu user interaction → klik tombol aktifkan suara |

---

*Dokumentasi dibuat oleh Claude Code*
*Last updated: 2026-05-17*
