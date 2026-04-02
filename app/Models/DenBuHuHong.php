<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DenBuHuHong extends Model
{
    protected $table = 'DenBuHuHong';
    protected $primaryKey = 'MaDenBu';
    public $timestamps = false;
    protected $guarded = [];
    public function datPhong()
    {
        return $this->belongsTo(DatPhong::class, 'MaDatPhong');
    }
    public function chiTietHoaDon()
    {
        return $this->hasOne(ChiTietHoaDon::class, 'MaDenBu');
    }
}
