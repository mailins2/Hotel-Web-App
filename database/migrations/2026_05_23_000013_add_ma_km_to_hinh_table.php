<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('Hinh') || Schema::hasColumn('Hinh', 'MaKM')) {
            return;
        }

        Schema::table('Hinh', function (Blueprint $table) {
            $table->string('MaKM', 10)->nullable()->after('MaDV');
            $table->foreign('MaKM', 'hinh_makm_foreign')
                ->references('MaKM')
                ->on('KhuyenMai')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('Hinh') || ! Schema::hasColumn('Hinh', 'MaKM')) {
            return;
        }

        Schema::table('Hinh', function (Blueprint $table) {
            $table->dropForeign('hinh_makm_foreign');
            $table->dropColumn('MaKM');
        });
    }
};
