<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Phong extends Model
{
    use SoftDeletes;

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
        return $this->belongsTo(LoaiPhong::class, 'MaLoaiPhong')->withTrashed();
    }
    public function chiTietDatPhong()
    {
        return $this->hasMany(ChiTietDatPhong::class, 'MaPhong');
    }

    public function luuTrus()
    {
        return $this->hasMany(LuuTru::class, 'MaPhong');
    }

}
