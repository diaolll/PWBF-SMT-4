<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    // SESUAIKAN: Tabel kamu di SQL bernama 'menu'
    protected $table = 'menu'; 
    
    // SESUAIKAN: Primary Key kamu adalah 'idmenu'
    protected $primaryKey = 'idmenu';
    
    // Kamu tidak pakai timestamps di SQL untuk tabel ini
    public $timestamps = false;

    protected $fillable = [
        'idvendor', 
        'nama_menu', 
        'harga', 
        'path_gambar'
    ];

    public function vendor()
    {
        // Relasi ke Vendor menggunakan foreign key 'idvendor'
        return $this->belongsTo(Vendor::class, 'idvendor', 'idvendor');
    }
}