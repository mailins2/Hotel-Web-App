<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    protected $table = 'HoaDon';
    protected $primaryKey = 'MaHD';
    public $timestamps = false;
    protected $fillable = [
        'MaDatPhong',
        'NgayLapHD',
        'MaKM',
        'TongTien',
        'MaNV',
        'TrangThai',
        'DaThanhToan'
    ];

    public function datPhong()
    {
        return $this->belongsTo(DatPhong::class, 'MaDatPhong');
    }
     public function chiTietHoaDons()
    {
        return $this->hasMany(ChiTietHoaDon::class, 'MaHD');
    }
    public function thanhToans()
    {
        return $this->hasMany(ThanhToan::class, 'MaHD');
    }

    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'MaNV');
    }
      public function khuyenMai()
    {
        return $this->belongsTo(KhuyenMai::class, 'MaKM');
    }
}
