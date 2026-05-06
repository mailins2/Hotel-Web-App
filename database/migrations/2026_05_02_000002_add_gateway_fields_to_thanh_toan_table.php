<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ThanhToan', function (Blueprint $table) {
            $table->string('NhaCungCap', 30)->default('manual')->after('LoaiThanhToan');
            $table->string('DinhDanhNguoiThanhToan', 120)->nullable()->after('NhaCungCap');
            $table->string('MaGiaoDich', 60)->nullable()->after('DinhDanhNguoiThanhToan');
            $table->string('MaGiaoDichCongThanhToan', 80)->nullable()->after('MaGiaoDich');
            $table->integer('TrangThaiGiaoDich')->default(1)->after('MaGiaoDichCongThanhToan');

            $table->unique('MaGiaoDich');
            $table->index(['NhaCungCap', 'TrangThaiGiaoDich']);
        });
    }

    public function down(): void
    {
        Schema::table('ThanhToan', function (Blueprint $table) {
            $table->dropUnique(['MaGiaoDich']);
            $table->dropIndex(['NhaCungCap', 'TrangThaiGiaoDich']);
            $table->dropColumn([
                'NhaCungCap',
                'DinhDanhNguoiThanhToan',
                'MaGiaoDich',
                'MaGiaoDichCongThanhToan',
                'TrangThaiGiaoDich',
            ]);
        });
    }
};
