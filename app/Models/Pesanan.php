<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'idpesanan'; // Sesuai SQL kamu
    public $timestamps = false; // Karena kamu pakai kolom 'timestamp' manual

    protected $fillable = [
        'nama', 'order_id', 'total', 'metode_bayar', 'status_bayar', 'snap_token'
    ];

    public function details()
    {
        return $this->hasMany(DetailPesanan::class, 'idpesanan', 'idpesanan');
    }
}