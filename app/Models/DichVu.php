<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DichVu extends Model
{
    use SoftDeletes;

    public const TYPE_FOOD_AND_BEVERAGE = 1;
    public const TYPE_ROOM_SERVICE = 2;
    public const TYPE_ENTERTAINMENT = 3;

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

    public function suDungs()
    {
        return $this->hasMany(SuDungDichVu::class, 'MaDV');
    }

    public function hinhs()
    {
        return $this->hasMany(Hinh::class, 'MaDV');
    }

    public function getLoaiDVTextAttribute()
    {
        return match ((int) $this->LoaiDV) {
            self::TYPE_FOOD_AND_BEVERAGE => 'Dịch vụ ăn uống',
            self::TYPE_ROOM_SERVICE => 'Dịch vụ phòng',
            self::TYPE_ENTERTAINMENT => 'Dịch vụ giải trí',
            default => 'Khác'
        };
    }

    public function getGiaDVFormattedAttribute()
    {
        return number_format((float) $this->GiaDV, 0, ',', '.') . ' VND';
    }
}
