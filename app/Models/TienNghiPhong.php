<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\LoaiPhong;
use App\Models\TienNghi;

class TienNghiPhong extends Model
{
    protected $table = 'TienNghiPhong';

    public $timestamps = false;

    // bảng này không có id riêng
    public $incrementing = false;

    protected $primaryKey = null;

    protected $fillable = [
        'MaLoaiPhong',
        'MaTienNghi'
    ];

    // thuộc về loại phòng
    public function loaiPhong()
    {
        return $this->belongsTo(LoaiPhong::class, 'MaLoaiPhong');
    }

    // thuộc về tiện nghi
    public function tienNghi()
    {
        return $this->belongsTo(TienNghi::class, 'MaTienNghi');
    }
}