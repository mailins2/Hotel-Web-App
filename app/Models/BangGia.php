<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\LoaiPhong;

class BangGia extends Model
{
    protected $table = 'BangGia';

    public $timestamps = false;

    // 🔥 QUAN TRỌNG
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'MaLoaiPhong',
        'Mua',
        'GiaPhong'
    ];

    public function loaiPhong()
    {
        return $this->belongsTo(LoaiPhong::class, 'MaLoaiPhong');
    }
}