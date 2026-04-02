<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TienNghiPhong extends Model
{
    protected $table = 'TienNghiPhong';
    public $timestamps = false;
    protected $guarded = [];
    public function loaiPhong()
    {
        return $this->belongsTo(LoaiPhong::class, 'MaLoaiPhong');
    }

    public function tienNghi()
    {
        return $this->belongsTo(TienNghi::class, 'MaTienNghi');
    }

}
