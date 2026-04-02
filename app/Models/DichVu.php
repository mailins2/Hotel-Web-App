<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DichVu extends Model
{
    protected $table = 'DichVu';
    protected $primaryKey = 'MaDV';
    public $timestamps = false;
    protected $guarded = [];
     public function suDungs()
    {
        return $this->hasMany(SuDungDichVu::class, 'MaDV');
    }
}
