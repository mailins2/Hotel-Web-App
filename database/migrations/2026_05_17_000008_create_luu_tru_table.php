<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('LuuTru')) {
            Schema::create('LuuTru', function (Blueprint $table) {
                $table->id('MaLuuTru');
                $table->string('TenKhach', 100);
                $table->date('NgaySinh');
                $table->string('CCCD', 20);
                $table->string('SoDienThoai', 15)->nullable();
                $table->unsignedBigInteger('MaPhong');
                $table->unsignedBigInteger('MaDatPhong');

                $table->foreign('MaDatPhong')
                    ->references('MaDatPhong')
                    ->on('DatPhong');

                $table->foreign('MaPhong')
                    ->references('MaPhong')
                    ->on('Phong');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('LuuTru');
    }
};
