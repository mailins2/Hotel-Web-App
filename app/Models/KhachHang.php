<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhachHang extends Model
{
    protected $table = 'KhachHang';
    protected $primaryKey = 'MaKH';
    public $timestamps = false;
    protected $fillable = [
        'MaTK',
        'TenKH',
        'SoDienThoai',
        'CCCD',
        'NgaySinh',
        'GioiTinh',
        'DiaChi'
    ];

    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'MaTK');
    }

    public function datPhongs()
    {
        return $this->hasMany(DatPhong::class, 'MaKH');
    }
       public function khoKhuyenMai()
    {
        return $this->hasMany(KhoKhuyenMai::class, 'MaKH');
    }
}
