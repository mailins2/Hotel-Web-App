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
        return $this->hasMany(SuDungDichVu::class, 'MaDatPhong');
    }
    public function chiTietDatPhong()
    {
        return $this->hasMany(ChiTietDatPhong::class, 'MaDatPhong');
    }

}