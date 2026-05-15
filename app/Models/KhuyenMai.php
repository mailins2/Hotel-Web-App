<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KhuyenMai extends Model
{
    use SoftDeletes;

    protected $table = 'KhuyenMai';
    protected $primaryKey = 'MaKM';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    public function khoKhuyenMai()
    {
        return $this->hasMany(KhoKhuyenMai::class, 'MaKM');
    }

    public function hoaDons()
    {
        return $this->hasMany(HoaDon::class, 'MaKM');
    }
}
