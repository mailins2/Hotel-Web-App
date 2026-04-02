<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhanVien extends Model
{
    protected $table = 'NhanVien';
    protected $primaryKey = 'MaNV';
    public $timestamps = false;
    protected $guarded = [];

    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'MaTK');
    }

    public function hoaDons()
    {
        return $this->hasMany(HoaDon::class, 'MaNV');
    }
}