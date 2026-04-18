<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietDatPhong extends Model
{
    protected $table = 'ChiTietDatPhong';
    protected $primaryKey = 'MaCTDP';
    public $timestamps = false;
     protected $fillable = [
        'MaDatPhong',
        'MaPhong',
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