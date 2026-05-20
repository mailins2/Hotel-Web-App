<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuDungDichVu extends Model
{
    protected $table = 'SuDungDichVu';
    protected $primaryKey = 'MaSuDung';
    public $timestamps = false;
     protected $fillable = [
        'MaCTDP',
        'MaDV',
        'SoLuong',
        'ThoiGian'
    ];

    public function chiTietDatPhong()
    {
        return $this->belongsTo(ChiTietDatPhong::class, 'MaCTDP');
    }

    public function dichVu()
    {
        return $this->belongsTo(DichVu::class, 'MaDV');
    }
    public function chiTietHoaDon()
    {
        return $this->hasMany(ChiTietHoaDon::class, 'MaSuDung');
    }

}
