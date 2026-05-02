<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Vendor, Pesanan, DetailPesanan, Menu};
use Midtrans\{Config, Snap};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KantinController extends Controller
{
    public function index()
    {
        $vendors = Vendor::all();
        return view('kantin.index', compact('vendors'));
    }

    public function menu($idvendor)
    {
        return response()->json(
            Menu::where('idvendor', $idvendor)->get()
        );
    }

    /**
     * Halaman Pesanan Saya (My Orders)
     * Menampilkan riwayat pesanan dari localStorage
     */
    public function orders()
    {
        return view('kantin.orders');
    }

public function retry($order_id)
{
    $pesanan = Pesanan::where('order_id', $order_id)->firstOrFail();

    Config::$serverKey = config('services.midtrans.server_key');
    Config::$isProduction = config('services.midtrans.is_production', false);

    $item_details = [];
    $total = 0;

    foreach ($pesanan->details as $d) {
        $item_details[] = [
            'id'       => (string) $d->idmenu,
            'price'    => (int) $d->harga,
            'quantity' => (int) $d->jumlah,
            'name'     => substr($d->menu->nama_menu ?? 'Menu', 0, 50),
        ];

        $total += $d->subtotal;
    }

    $newOrderId = 'INV-' . $pesanan->idpesanan;

    $pesanan->update([
        'order_id' => $newOrderId,
        'status_bayar' => 0
    ]);

    $params = [
        'transaction_details' => [
            'order_id' => $newOrderId,
            'gross_amount' => (int) $total,
        ],
        'item_details' => $item_details,
        'customer_details' => [
            'first_name' => $pesanan->nama ?? 'Customer',
        ],
    ];

    $snapToken = Snap::getSnapToken($params);

    $pesanan->update([
        'snap_token' => $snapToken
    ]);

    return response()->json([
        'token' => $snapToken
    ]);
}

public function checkout(Request $request)
{
    Log::info("Checkout called", $request->all());

    $request->validate([
        'cart' => 'required|array|min:1'
    ]);

    Config::$serverKey = config('services.midtrans.server_key');
    Config::$isProduction = config('services.midtrans.is_production', false);
    Config::$isSanitized = true;
    Config::$is3ds = true;

    if (!Config::$serverKey) {
        return response()->json(['message' => 'Server Key kosong'], 500);
    }

    try {
        return DB::transaction(function () use ($request) {

            $total = 0;
            $item_details = [];

            foreach ($request->cart as $item) {
                $harga = (int) $item['harga'];
                $qty   = (int) $item['qty'];
                $nama  = $item['nama'] ?? 'Menu';

                $subtotal = $harga * $qty;
                $total += $subtotal;

                $item_details[] = [
                    'id'       => (string) $item['idmenu'],
                    'price'    => $harga,
                    'quantity' => $qty,
                    'name'     => substr($nama, 0, 50),
                ];
            }

            $pesanan = Pesanan::create([
                'nama'         => auth()->user()->name ?? 'Customer',
                'total'        => $total,
                'status_bayar' => 0,
                'metode_bayar' => 0,
            ]);

            $orderId = 'INV-' . $pesanan->idpesanan;

            $pesanan->update([
                'order_id' => $orderId
            ]);

            Log::info("ORDER FIXED", ['order_id' => $orderId]);

            foreach ($request->cart as $item) {
                DetailPesanan::create([
                    'idpesanan' => $pesanan->idpesanan,
                    'idmenu'    => $item['idmenu'],
                    'jumlah'    => $item['qty'],
                    'harga'     => $item['harga'],
                    'subtotal'  => $item['qty'] * $item['harga'],
                    'catatan'   => $item['catatan'] ?? '',
                ]);
            }
            $params = [
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => (int) $total,
                ],
                'item_details' => $item_details,
                'customer_details' => [
                    'first_name' => auth()->user()->name ?? 'Customer',
                ],

                // Semua payment method yang available
                'enabled_payments' => [
                    'qris',          // QR Code untuk studi kasus
                    'gopay',
                    'shopeepay',
                    'bank_transfer',
                    'permata',
                    'bca_va',
                    'bni_va',
                    'bri_va',
                    'mandiri_bill',
                    'alfamart',
                    'indomaret',
                ],

                // Callback URLs — include order_id di finish URL
                'callbacks' => [
                    'finish'   => url('/kantin/sukses?order_id=') . $orderId,
                    'unfinish' => url('/kantin'),
                    'error'    => url('/kantin'),
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            $pesanan->update([
                'snap_token' => $snapToken
            ]);

            // Simpan order_id di session untuk redirect setelah pembayaran
            session(['last_order_id' => $orderId]);

            return response()->json([
                'status'   => 'success',
                'token'    => $snapToken,
                'order_id' => $orderId  // ← kirim order_id ke frontend
            ]);
        });

    } catch (\Exception $e) {

        Log::error("Checkout Error: " . $e->getMessage());

        return response()->json([
            'message' => 'Checkout gagal',
            'error'   => $e->getMessage()
        ], 500);
    }
}

    /**
     * Halaman sukses setelah pembayaran
     */
    public function sukses($idpesanan = null)
    {
        $pesanan = null;

        if ($idpesanan) {
            $pesanan = Pesanan::with('details.menu')->find($idpesanan);
        }

        return view('kantin.sukses', compact('pesanan'));
    }
}