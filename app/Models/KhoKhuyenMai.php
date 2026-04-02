<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhoKhuyenMai extends Model
{
    protected $table = 'KhoKhuyenMai';
    public $timestamps = false;
    protected $guarded = [];
     public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'MaKH');
    }
    public function khuyenMai()
    {
        return $this->belongsTo(KhuyenMai::class, 'MaKM');
    }

}
