<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietDatPhong extends Model
{
    protected $table = 'ChiTietDatPhong';
    protected $primaryKey = 'MaCTDP';
    public $timestamps = false;

    const BOOKED = 0;
    const CHECKED_IN = 1;
    const CHECKED_OUT = 2;
    const CANCELLED = 3;

    protected $fillable = [
        'MaDatPhong',
        'MaPhong',
        'TrangThai',
    ];

    protected $casts = [
        'TrangThai' => 'integer',
    ];


    public function phong()
    {
        return $this->belongsTo(Phong::class, 'MaPhong');
    }
    public function datPhong()
    {
        return $this->belongsTo(DatPhong::class, 'MaDatPhong');
    }
}
