<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class TaiKhoan extends Model
{
     use HasApiTokens;
    protected $table = 'TaiKhoan';
    protected $primaryKey = 'MaTK';
    public $timestamps = false;
    protected $guarded = [];

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'MaKH', 'MaKH');
    }

    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'MaNV', 'MaNV');
    }
}
