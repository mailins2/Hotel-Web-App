<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class LoaiPhong extends Model
{

    protected $table = 'LoaiPhong';
    protected $primaryKey = 'MaLoaiPhong';
    public $timestamps = false;
    protected $appends = [
        'GiaGiam',
    ];

    protected $fillable = [
        'TenLoaiPhong',
        'Mota',
        'NguoiLon',
        'TreEm',
        'GiaPhong',
        'MaKM',
    ];

    protected $casts = [
        'NguoiLon' => 'integer',
        'TreEm' => 'integer',
        'GiaPhong' => 'decimal:2',
    ];

    public function phongs()
    {
        return $this->hasMany(Phong::class, 'MaLoaiPhong');
    }
    public function khuyenMai()
    {
        return $this->belongsTo(KhuyenMai::class, 'MaKM', 'MaKM')->withTrashed();
    }

    public function getGiaGiamAttribute(): float
    {
        return $this->giaSauKhuyenMai();
    }

    public function giaSauKhuyenMai($date = null): float
    {
        $giaPhong = (float) ($this->GiaPhong ?? 0);
        $khuyenMai = $this->khuyenMaiConHan($date);

        if (!$khuyenMai) {
            return round($giaPhong, 2);
        }

        $phanTramGiam = min(max((float) ($khuyenMai->PhanTramGiamGia ?? 0), 0), 100);

        return round($giaPhong * (100 - $phanTramGiam) / 100, 2);
    }

    public function khuyenMaiConHan($date = null): ?KhuyenMai
    {
        if (!$this->MaKM) {
            return null;
        }

        $today = $date ? Carbon::parse($date)->toDateString() : Carbon::today()->toDateString();
        $khuyenMai = $this->relationLoaded('khuyenMai')
            ? $this->getRelation('khuyenMai')
            : $this->khuyenMai()->first();

        if (!$khuyenMai || $khuyenMai->trashed()) {
            return null;
        }

        if (!$khuyenMai->NgayBatDau || !$khuyenMai->NgayKetThuc) {
            return null;
        }

        $ngayBatDau = Carbon::parse($khuyenMai->NgayBatDau)->toDateString();
        $ngayKetThuc = Carbon::parse($khuyenMai->NgayKetThuc)->toDateString();

        return $ngayBatDau <= $today && $ngayKetThuc >= $today
            ? $khuyenMai
            : null;
    }
    public function chiTietHoaDons()
    {
        return $this->hasMany(ChiTietHoaDon::class, 'MaLoaiPhong');
    }
    public function hinhs()
    {
        return $this->hasMany(Hinh::class, 'MaLoaiPhong');
    }
    public function tienNghis()
    {
        return $this->belongsToMany(
            TienNghi::class,
            'TienNghiPhong',
            'MaLoaiPhong',
            'MaTienNghi'
        );
    }
}
