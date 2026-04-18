<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DichVu extends Model
{
    protected $table = 'DichVu';
    protected $primaryKey = 'MaDV';
    public $timestamps = false;

    protected $fillable = [
        'TenDV',
        'GiaDV',
        'LoaiDV'
    ];

    protected $appends = [
        'LoaiDVText',
        'GiaDVFormatted'
    ];

    // ================= RELATION =================
    public function suDungs()
    {
        return $this->hasMany(SuDungDichVu::class, 'MaDV');
    }

    public function hinhs()
    {
        return $this->hasMany(Hinh::class, 'MaDV');
    }

    // ================= ACCESSOR =================
    public function getLoaiDVTextAttribute()
    {
        return match($this->LoaiDV) {
            1 => 'DỊCH VỤ ĂN UỐNG',
            2 => 'DỊCH VỤ PHÒNG',
            3 => 'DỊCH VỤ GIẢI TRÍ',
            default => 'KHÁC'
        };
    }

    public function getGiaDVFormattedAttribute()
    {
        return number_format($this->GiaDV, 0, ',', '.') . ' VND';
    }
}