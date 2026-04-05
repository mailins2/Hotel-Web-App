<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hinh extends Model
{
    protected $table = 'Hinh';    
    protected $primaryKey = 'Id';

    public function loaiPhongs()
    {
        return $this->belongsTo(LoaiPhong::class, 'MaLoaiPhong');
    }
    public function dichVus()
    {
        return $this->belongsTo(DichVu::class, 'MaDV');
    }
}
