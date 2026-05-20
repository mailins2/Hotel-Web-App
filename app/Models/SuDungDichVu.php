<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuDungDichVu extends Model
{
    protected $table = 'SuDungDichVu';
    protected $primaryKey = 'MaSuDung';
    public $timestamps = false;
     protected $fillable = [
        'MaDatPhong',
        'MaDV',
        'SoLuong',
        'ThoiGian'
    ];

    public function datPhong()
    {
        return $this->belongsTo(DatPhong::class, 'MaDatPhong');
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
