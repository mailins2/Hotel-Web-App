<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhuyenMai extends Model
{
    protected $table = 'KhuyenMai';
    protected $primaryKey = 'MaKM';
    public $timestamps = false;
    protected $guarded = [];

    public function khoKhuyenMai()
    {
        return $this->hasMany(KhoKhuyenMai::class, 'MaKM');
    }
}
