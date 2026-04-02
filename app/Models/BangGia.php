<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BangGia extends Model
{
    protected $table = 'BangGia';
    public $timestamps = false;
    protected $guarded = [];
    public function loaiPhong()
    {
        return $this->belongsTo(LoaiPhong::class, 'MaLoaiPhong');
    }

}
