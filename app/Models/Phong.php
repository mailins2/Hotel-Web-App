<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phong extends Model
{
    protected $table = 'Phong';
    protected $primaryKey = 'MaPhong';
    public $timestamps = false;
      protected $fillable = [
        'SoPhong',
        'MaLoaiPhong',
        'TinhTrang'
    ];

    public function loaiPhong()
    {
        return $this->belongsTo(LoaiPhong::class, 'MaLoaiPhong');
    }
    public function chiTietDatPhong()
    {
        return $this->hasMany(ChiTietDatPhong::class, 'MaPhong');
    }

}
