<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KriteriaBobot extends Model
{
    use HasFactory;

    protected $table = 'kriteria_bobots';
    protected $primaryKey = 'id'; // default laravel sudah 'id', boleh dihapus juga
    protected $fillable = ['kriteria_id','l','m','u','defuzzifikasi','prioritas'];

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id', 'id_kriteria');
    }
}
