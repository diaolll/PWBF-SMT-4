<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use Midtrans\Config;
use Midtrans\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class PaymentController extends Controller
{
    private function mapMetodeBayar($paymentType)
    {
        $map = [
            'qris'            => 1,
            'bank_transfer'   => 2,
            'echannel'        => 3, // Mandiri Bill
            'cstore'          => 4, // Alfamart/Indomaret
            'gopay'           => 5,
            'shopeepay'       => 6,
            'credit_card'     => 7,
        ];

        return $map[strtolower($paymentType)] ?? 0;
    }

    /**
     * Helper untuk memetakan status transaksi Midtrans ke status_bayar (Integer)
     */
    private function mapStatusBayar($transactionStatus)
    {
        if (in_array($transactionStatus, ['settlement', 'capture'])) {
            return 1; // Berhasil
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            return 2; // Gagal / Batal
        }
        return 0; // Pending / Belum Bayar
    }

    /**
     * CALLBACK (Otomatis dari Midtrans ke Server)
     */
    public function callback(Request $request)
    {
        try {
            $notif = $request->all();
            Log::info("CALLBACK MASUK", $notif);

            $serverKey = config('services.midtrans.server_key');

            // VALIDASI DASAR
            if (!isset($notif['order_id'], $notif['status_code'], $notif['gross_amount'])) {
                return response()->json(['message' => 'Data tidak lengkap'], 400);
            }

            // VALIDASI SIGNATURE
            $signature = hash("sha512",
                $notif['order_id'] .
                $notif['status_code'] .
                $notif['gross_amount'] .
                $serverKey
            );

            if ($signature !== ($notif['signature_key'] ?? '')) {
                Log::error("INVALID SIGNATURE", $notif);
                return response()->json(['message' => 'Invalid Signature'], 403);
            }

            Log::info("CARI ORDER", ['order_id' => $notif['order_id']]);

            $pesanan = Pesanan::where('order_id', $notif['order_id'])->first();

            if (!$pesanan) {
                Log::error("ORDER TIDAK DITEMUKAN", $notif);
                return response()->json(['message' => 'Order not found'], 404);
            }

            // MAPPING
            $status_bayar = $this->mapStatusBayar($notif['transaction_status'] ?? 'pending');
            $metode_bayar = $this->mapMetodeBayar($notif['payment_type'] ?? '');

            $pesanan->update([
                'status_bayar' => $status_bayar,
                'metode_bayar' => $metode_bayar
            ]);

            Log::info("UPDATE BERHASIL", [
                'order_id' => $notif['order_id'],
                'status'   => $status_bayar,
                'metode'   => $metode_bayar
            ]);

            return response()->json(['status' => 'OK']);

        } catch (\Exception $e) {
            Log::error("CALLBACK ERROR: " . $e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function index()
    {
        // Mengambil pesanan lunas, urutkan dari yang terbaru
        $pesanan = Pesanan::with('details.menu')
                    ->where('status_bayar', 1)
                    ->orderBy('timestamp', 'desc')
                    ->get();

        return view('pesanan.lunas', compact('pesanan'));
    }

    public function show($order_id)
    {
        $pesanan = Pesanan::with('details.menu')
                    ->where('order_id', $order_id)
                    ->firstOrFail();

        return view('pesanan.detail', compact('pesanan'));
    }

    /**
     * API: Mendapatkan data pesanan untuk QR Code Scanner
     * GET /api/pesanan/{order_id}
     */
    public function getPesanan($order_id)
    {
        $pesanan = Pesanan::with('details.menu')
                    ->where('order_id', $order_id)
                    ->first();

        if (!$pesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        // Format items
        $items = [];
        foreach ($pesanan->details as $detail) {
            $items[] = [
                'nama' => $detail->menu->nama_menu ?? 'Menu',
                'jumlah' => $detail->jumlah,
                'harga' => $detail->harga,
                'subtotal' => 'Rp ' . number_format($detail->subtotal, 0, ',', '.'),
                'metode_bayar' => $pesanan->metode_bayar
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $pesanan->order_id,
                'nama' => $pesanan->nama,
                'total' => $pesanan->total,
                'status_bayar' => $pesanan->status_bayar,
                'metode_bayar' => $pesanan->metode_bayar,
                'items' => $items
            ]
        ]);
    }

    /**
     * CEK STATUS MANUAL (Untuk testing di Localhost)
     */
    public function checkStatus($order_id)
    {
        $serverKey = config('services.midtrans.server_key');
        $isProduction = config('services.midtrans.is_production', false);
        $baseUrl = $isProduction
            ? "https://api.midtrans.com/v2"
            : "https://api.sandbox.midtrans.com/v2";

        $auth = base64_encode($serverKey . ':');

        try {
            $response = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => "Basic $auth",
            ])->get("$baseUrl/$order_id/status");

            $data = $response->json();

            if (isset($data['status_code']) && $data['status_code'] == "404") {
                return back()->with('error', 'Order ID tidak ditemukan di Midtrans.');
            }

            $pesanan = Pesanan::where('order_id', $order_id)->first();

            if (!$pesanan) {
                return back()->with('error', 'Pesanan tidak ditemukan di database.');
            }

            // AMBIL DATA DARI RESPONSE API
            $transactionStatus = $data['transaction_status'] ?? 'pending';
            $paymentType = $data['payment_type'] ?? '';

            // MAPPING STATUS & METODE
            $status = $this->mapStatusBayar($transactionStatus);
            $metode = $this->mapMetodeBayar($paymentType);

            // UPDATE DATABASE
            $pesanan->update([
                'status_bayar' => $status,
                'metode_bayar' => $metode
            ]);

            $labelStatus = ($status == 1) ? 'BERHASIL' : (($status == 2) ? 'GAGAL' : 'PENDING');

            return back()->with('success', "Status $order_id diperbarui ke $labelStatus (Metode: $paymentType)");

        } catch (\Exception $e) {
            Log::error('Check Status Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengecek status.');
        }
    }

    /**
     * Halaman Sukses dengan QR Code
     * Menerima order_id dari callback Midtrans
     */
    public function sukses(Request $request)
    {
        // Ambil dari query parameter atau session (fallback)
        $orderId = $request->query('order_id') ?? session('last_order_id');

        Log::info("SUKSES PAGE CALLED", [
            'query_order_id' => $request->query('order_id'),
            'session_order_id' => session('last_order_id'),
            'final_order_id' => $orderId
        ]);

        if (!$orderId) {
            return redirect()->route('kantin.index')->with('error', 'Order tidak ditemukan');
        }

        // Hapus session setelah digunakan
        session()->forget('last_order_id');

        $pesanan = Pesanan::with('details.menu')
                    ->where('order_id', $orderId)
                    ->first();

        if (!$pesanan) {
            Log::error("PESANAN NOT FOUND", ['order_id' => $orderId]);
            return redirect()->route('kantin.index')->with('error', 'Pesanan tidak ditemukan');
        }

        // QR Code berisi URL ke detail pesanan — jadi saat discan langsung buka detail
        $qrUrl    = url('/pesanan/detail/' . $pesanan->order_id);
        $qrCode   = new QrCode($qrUrl);
        $writer   = new PngWriter();
        $result   = $writer->write($qrCode);
        $qrBase64 = base64_encode($result->getString());

        Log::info("RENDER SUKSES PAGE", ['order_id' => $orderId, 'idpesanan' => $pesanan->idpesanan]);

        return view('kantin.sukses', compact('pesanan', 'qrBase64'));
    }
}