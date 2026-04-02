<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TienNghi extends Model
{
    protected $table = 'TienNghi';
    protected $primaryKey = 'MaTienNghi';
    public $timestamps = false;
    protected $guarded = [];
    public function tienNghiPhongs()
    {
        return $this->hasMany(TienNghiPhong::class, 'MaTienNghi');
    }

}
