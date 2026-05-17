<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhachHang extends Model
{
    protected $table = 'KhachHang';
    protected $primaryKey = 'MaKH';
    public $timestamps = false;
    protected $fillable = [
        'TenKH',
        'SoDienThoai',
        'CCCD',
        'NgaySinh',
        'GioiTinh',
        'DiaChi',
        'DIEM',
    ];

    public function taiKhoan()
    {
        return $this->hasOne(TaiKhoan::class, 'MaKH', 'MaKH');
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
