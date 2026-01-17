<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subkriteria extends Model
{
    use HasFactory;

    protected $table = 'subkriteria';
    protected $primaryKey = 'id_subkriteria';

    protected $fillable = [
        'id_kriteria',
        'nama_subkriteria',
        'nilai',
        'jenis_saran',
        'min_ketebalan_saran',
        'max_ketebalan_saran',
        'status',
        'added_by',
        'approved_at',
    ];

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'id_kriteria', 'id_kriteria');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

}



