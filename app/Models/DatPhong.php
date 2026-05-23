<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatPhong extends Model
{
    protected $table = 'DatPhong';
    protected $primaryKey = 'MaDatPhong';
    public $timestamps = false;
    protected $fillable = [
        'MaKH',
        'NgayDat',
        'NgayNhanPhong',
        'NgayTraPhong',
        'SoLuong',
        'TinhTrang'
    ];
     // 🔥 TRẠNG THÁI BOOKING
    const HOLD = 0;        // Đang giữ chỗ (15 phút)
    const CONFIRMED = 1;   // Đã xác nhận/đặt cọc
    const CHECKED_IN = 2;  // Đang ở
    const CHECKED_OUT = 3; // Đã trả phòng
    const CANCELLED = 4;   // Đã hủy (hết hạn hoặc user hủy)

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'MaKH');
    }

    public function hoaDon()
    {
        return $this->hasOne(HoaDon::class, 'MaDatPhong');
    }
    public function denBuHuHong()
    {
        return $this->hasOne(DenBuHuHong::class, 'MaDatPhong');
    }
    public function danhGia()
    {
        return $this->hasOne(DanhGia::class, 'MaDatPhong');
    }
    public function suDungDichVu()
    {
        return $this->hasManyThrough(
            SuDungDichVu::class,
            ChiTietDatPhong::class,
            'MaDatPhong',
            'MaCTDP',
            'MaDatPhong',
            'MaCTDP'
        );
    }
    public function chiTietDatPhong()
    {
        return $this->hasMany(ChiTietDatPhong::class, 'MaDatPhong');
    }

    public function luuTrus()
    {
        return $this->hasMany(LuuTru::class, 'MaDatPhong');
    }
    

}
