<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietHoaDon extends Model
{
    protected $table = 'ChiTietHoaDon';
    protected $primaryKey = 'MaCTHD';
    public $timestamps = false;
    protected $guarded = [];
    public function hoaDon()
    {
        return $this->belongsTo(HoaDon::class, 'MaHD');
    }
     public function denBu()
    {
        return $this->belongsTo(DenBuHuHong::class, 'MaDenBu');
    }
        public function suDung()
    {
        return $this->belongsTo(SuDungDichVu::class, 'MaSuDung');
    }
    public function loaiPhong()
    {
        return $this->belongsTo(LoaiPhong::class, 'MaLoaiPhong');
    }
}
