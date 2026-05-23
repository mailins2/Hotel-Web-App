<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('DatPhong', function (Blueprint $table) {
            $table->index(['TinhTrang', 'NgayNhanPhong', 'NgayTraPhong'], 'idx_datphong_status_stay_dates');
            $table->index(['TinhTrang', 'NgayTraPhong'], 'idx_datphong_status_checkout_date');
        });

        Schema::table('ChiTietDatPhong', function (Blueprint $table) {
            $table->index(['TrangThai', 'MaDatPhong', 'MaPhong'], 'idx_ctdp_status_booking_room');
        });

        Schema::table('Phong', function (Blueprint $table) {
            $table->index(['TinhTrang', 'SoPhong'], 'idx_phong_status_number');
        });

        Schema::table('SuDungDichVu', function (Blueprint $table) {
            $table->index(['MaCTDP', 'ThoiGian'], 'idx_sddv_room_detail_time');
            $table->index(['ThoiGian', 'MaSuDung'], 'idx_sddv_time_usage');
        });

        Schema::table('HoaDon', function (Blueprint $table) {
            $table->index(['TrangThai', 'NgayLapHD'], 'idx_hoadon_status_date');
        });

        Schema::table('ThanhToan', function (Blueprint $table) {
            $table->index(['NgayThanhToan', 'MaHD'], 'idx_thanhtoan_time_invoice');
        });
    }

    public function down(): void
    {
        Schema::table('ThanhToan', function (Blueprint $table) {
            $table->dropIndex('idx_thanhtoan_time_invoice');
        });

        Schema::table('HoaDon', function (Blueprint $table) {
            $table->dropIndex('idx_hoadon_status_date');
        });

        Schema::table('SuDungDichVu', function (Blueprint $table) {
            $table->dropIndex('idx_sddv_room_detail_time');
            $table->dropIndex('idx_sddv_time_usage');
        });

        Schema::table('Phong', function (Blueprint $table) {
            $table->dropIndex('idx_phong_status_number');
        });

        Schema::table('ChiTietDatPhong', function (Blueprint $table) {
            $table->dropIndex('idx_ctdp_status_booking_room');
        });

        Schema::table('DatPhong', function (Blueprint $table) {
            $table->dropIndex('idx_datphong_status_stay_dates');
            $table->dropIndex('idx_datphong_status_checkout_date');
        });
    }
};
