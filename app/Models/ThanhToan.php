<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThanhToan extends Model
{
    protected $table = 'ThanhToan';
    protected $primaryKey = 'MaTT';
    public $timestamps = false;
    protected $guarded = [];

    public function hoaDon()
    {
        return $this->belongsTo(HoaDon::class, 'MaHD');
    }
}
