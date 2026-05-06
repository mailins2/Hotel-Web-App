<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DatPhong;
use App\Models\ChiTietDatPhong;
use Carbon\Carbon;

class CancelExpiredBookings extends Command
{
    protected $signature = 'bookings:cancel-expired';
    protected $description = 'Hủy booking hết hạn';

    public function handle()
    {
        $expiredTime = Carbon::now()->subMinutes(15);
        
        $expiredBookings = DatPhong::where('TinhTrang', 0)
            ->where('NgayDat', '<=', $expiredTime)
            ->get();

        foreach ($expiredBookings as $booking) {
            \DB::transaction(function () use ($booking) {
                $booking->update(['TinhTrang' => 4]);
                ChiTietDatPhong::where('MaDatPhong', $booking->MaDatPhong)->delete();
                \App\Models\HoaDon::where('MaDatPhong', $booking->MaDatPhong)->update(['TrangThai' => 3]);
            });
            $this->info("Đã hủy booking #{$booking->MaDatPhong}");
        }

        $this->info("Hoàn tất! Đã hủy {$expiredBookings->count()} booking.");
    }
}