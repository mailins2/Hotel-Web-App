<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ChiTietDatPhong') || Schema::hasColumn('ChiTietDatPhong', 'TrangThai')) {
            return;
        }

        Schema::table('ChiTietDatPhong', function (Blueprint $table) {
            $table->unsignedTinyInteger('TrangThai')
                ->default(0)
                ->comment('0: booked, 1: checked_in, 2: checked_out, 3: cancelled');

            $table->index(['MaDatPhong', 'TrangThai'], 'ctdp_booking_status_index');
            $table->index(['MaPhong', 'TrangThai'], 'ctdp_room_status_index');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('ChiTietDatPhong') || !Schema::hasColumn('ChiTietDatPhong', 'TrangThai')) {
            return;
        }

        Schema::table('ChiTietDatPhong', function (Blueprint $table) {
            $table->dropIndex('ctdp_booking_status_index');
            $table->dropIndex('ctdp_room_status_index');
            $table->dropColumn('TrangThai');
        });
    }
};
