<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    use HasFactory;

    protected $table      = 'penjualan_detail';
    protected $primaryKey = 'idpenjualan_detail';
    public $incrementing  = true;
    protected $keyType    = 'int';
    public $timestamps    = false;

    protected $fillable = [
        'id_penjualan',
        'id_barang',
        'jumlah',
        'subtotal',
    ];

    // -------------------------------------------------------------------------
    // Relasi: Detail ini milik satu transaksi penjualan
    // -------------------------------------------------------------------------
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }

    // -------------------------------------------------------------------------
    // Relasi: Detail ini merujuk ke satu barang
    // -------------------------------------------------------------------------
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}