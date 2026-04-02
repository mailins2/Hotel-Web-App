<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    protected $table = 'HoaDon';
    protected $primaryKey = 'MaHD';
    public $timestamps = false;
    protected $guarded = [];

    public function datPhong()
    {
        return $this->belongsTo(DatPhong::class, 'MaDatPhong');
    }
}
