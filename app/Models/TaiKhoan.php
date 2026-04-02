<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaiKhoan extends Model
{
    protected $table = 'TaiKhoan';
    protected $primaryKey = 'MaTK';
    public $timestamps = false;
    protected $guarded = [];

    public function khachHang()
    {
        return $this->hasOne(KhachHang::class, 'MaTK');
    }

    public function nhanVien()
    {
        return $this->hasOne(NhanVien::class, 'MaTK');
    }
}
