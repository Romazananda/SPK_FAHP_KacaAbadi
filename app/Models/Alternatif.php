<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alternatif extends Model
{
    use HasFactory;

    protected $table = 'alternatifs';

    protected $fillable = [
        'nama', 'jenis', 'ukuran', 'ketebalan', 'finishing', 'harga',
        'tujuan_penggunaan', 'lokasi_penempatan',
        'jumlah_unit', 'pemotongan', 'pengiriman'
    ];
    public function hasil()
    {
        return $this->hasOne(HasilPerhitungan::class);
    }
    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'id_alternatif', 'id');
    }
}

