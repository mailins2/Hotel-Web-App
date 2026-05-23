<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhuyenMai extends Model
{
    protected $table = 'KhuyenMai';
    protected $primaryKey = 'MaKM';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    // 👈 THAY BẰNG CÁCH NÀY
    public static function bootSoftDeletes()
    {
        // Override rỗng - không cho SoftDeletes chạy
    }

    public function khoKhuyenMai()
    {
        return $this->hasMany(KhoKhuyenMai::class, 'MaKM');
    }

    public function hoaDons()
    {
        return $this->hasMany(HoaDon::class, 'MaKM');
    }
}