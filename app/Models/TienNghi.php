<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TienNghi extends Model
{
    use SoftDeletes;

    protected $table = 'TienNghi';
    protected $primaryKey = 'MaTienNghi';
    public $timestamps = false;
    protected $fillable = [
        'TenTienNghi'
    ];

    /**
     * MANY TO MANY: Tiện nghi ↔ Loại phòng
     */
    public function loaiPhongs()
    {
        return $this->belongsToMany(
            LoaiPhong::class,
            'TienNghiPhong',   // bảng trung gian
            'MaTienNghi',      // FK của bảng này
            'MaLoaiPhong'      // FK của bảng kia
        );
    }

}
