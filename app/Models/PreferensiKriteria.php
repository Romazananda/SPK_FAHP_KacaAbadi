<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreferensiKriteria extends Model
{
    use HasFactory;

    protected $table = 'preferensi_kriteria';

    protected $fillable = [
        'kriteria',
        'tujuan',
        'lokasi',
        'jenis_kaca',
        'finishing',
        'ketebalan_min',
        'ketebalan_maks',
        'nilai_kecocokan',
    ];
}
