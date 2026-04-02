<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    protected $table = 'DanhGia';
    protected $primaryKey = 'MaDG';
    public $timestamps = false;
    protected $guarded = [];
     public function datPhong()
    {
        return $this->belongsTo(DatPhong::class, 'MaDatPhong');
    }
}
