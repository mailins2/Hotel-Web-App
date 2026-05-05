<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('LoaiPhong', 'SoNguoiToiDa') && ! Schema::hasColumn('LoaiPhong', 'NguoiLon')) {
            Schema::table('LoaiPhong', function (Blueprint $table) {
                $table->renameColumn('SoNguoiToiDa', 'NguoiLon');
            });
        }

        if (! Schema::hasColumn('LoaiPhong', 'TreEm')) {
            Schema::table('LoaiPhong', function (Blueprint $table) {
                $table->integer('TreEm')->default(0)->after('NguoiLon');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('LoaiPhong', 'TreEm')) {
            Schema::table('LoaiPhong', function (Blueprint $table) {
                $table->dropColumn('TreEm');
            });
        }

        if (Schema::hasColumn('LoaiPhong', 'NguoiLon') && ! Schema::hasColumn('LoaiPhong', 'SoNguoiToiDa')) {
            Schema::table('LoaiPhong', function (Blueprint $table) {
                $table->renameColumn('NguoiLon', 'SoNguoiToiDa');
            });
        }
    }
};
