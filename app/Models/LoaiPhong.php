<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoaiPhong extends Model
{
    protected $table = 'LoaiPhong';
    protected $primaryKey = 'MaLoaiPhong';
    public $timestamps = false;
    protected $guarded = [];

    public function phongs()
    {
        return $this->hasMany(Phong::class, 'MaLoaiPhong');
    }
        public function bangGias()
    {
        return $this->hasMany(BangGia::class, 'MaLoaiPhong');
    }
    public function chiTietHoaDons()
    {
        return $this->hasMany(ChiTietHoaDon::class, 'MaLoaiPhong');
    }
}
