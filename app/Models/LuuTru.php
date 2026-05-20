<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LuuTru extends Model
{
    protected $table = 'LuuTru';
    protected $primaryKey = 'MaLuuTru';
    public $timestamps = false;

    protected $fillable = [
        'TenKhach',
        'NgaySinh',
        'CCCD',
        'SoDienThoai',
        'MaPhong',
        'MaDatPhong',
    ];

    protected $casts = [
        'NgaySinh' => 'date:Y-m-d',
    ];

    public function phong()
    {
        return $this->belongsTo(Phong::class, 'MaPhong');
    }

    public function datPhong()
    {
        return $this->belongsTo(DatPhong::class, 'MaDatPhong');
    }
}
