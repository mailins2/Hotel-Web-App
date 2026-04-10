<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhoKhuyenMai extends Model
{
    protected $table = 'KhoKhuyenMai';
    public $timestamps = false;
    protected $guarded = [];
    
    // Thêm dòng này vì bảng không có cột 'id' tăng tự động
    public $incrementing = false; 
    protected $primaryKey = ['MaKM', 'MaKH'];
    
    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'MaKH');
    }
    public function khuyenMai()
    {
        return $this->belongsTo(KhuyenMai::class, 'MaKM');
    }
}
