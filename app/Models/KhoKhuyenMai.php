<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhoKhuyenMai extends Model
{
    protected $table = 'KhoKhuyenMai';
    public $timestamps = false;
    protected $guarded = [];
    
    public $incrementing = false;
    
    // 🔥 SỬA: Không dùng array cho primaryKey
    // protected $primaryKey = ['MaKM', 'MaKH']; // 👈 XÓA DÒNG NÀY
    
    // 🔥 THÊM: Override cho composite key
    protected function setKeysForSaveQuery($query)
    {
        $query->where('MaKM', $this->MaKM)
              ->where('MaKH', $this->MaKH);
        return $query;
    }
    
    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'MaKH', 'MaKH');
    }
    
    public function khuyenMai()
    {
        return $this->belongsTo(KhuyenMai::class, 'MaKM', 'MaKM')->withTrashed();
    }
}