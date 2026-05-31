<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('Phong') || !Schema::hasColumn('Phong', 'TinhTrang')) {
            return;
        }

        try {
            Schema::table('Phong', function (Blueprint $table) {
                $table->dropIndex('idx_phong_status_number');
            });
        } catch (Throwable) {
            // The index only exists after the reception performance indexes migration.
        }

        Schema::table('Phong', function (Blueprint $table) {
            $table->dropColumn('TinhTrang');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('Phong') || Schema::hasColumn('Phong', 'TinhTrang')) {
            return;
        }

        Schema::table('Phong', function (Blueprint $table) {
            $table->integer('TinhTrang')->default(0)->after('MaLoaiPhong');
            $table->index(['TinhTrang', 'SoPhong'], 'idx_phong_status_number');
        });
    }
};
