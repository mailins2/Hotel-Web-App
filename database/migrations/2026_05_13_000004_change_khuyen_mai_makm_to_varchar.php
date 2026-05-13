<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE KhoKhuyenMai DROP FOREIGN KEY khokhuyenmai_makm_foreign');
        DB::statement('ALTER TABLE HoaDon DROP FOREIGN KEY hoadon_makm_foreign');
        DB::statement('ALTER TABLE KhoKhuyenMai DROP PRIMARY KEY');

        DB::statement('ALTER TABLE KhuyenMai MODIFY MaKM VARCHAR(10) NOT NULL');
        DB::statement('ALTER TABLE KhoKhuyenMai MODIFY MaKM VARCHAR(10) NOT NULL');
        DB::statement('ALTER TABLE HoaDon MODIFY MaKM VARCHAR(10) NULL');

        DB::statement('ALTER TABLE KhoKhuyenMai ADD PRIMARY KEY (MaKM, MaKH)');
        DB::statement('ALTER TABLE KhoKhuyenMai ADD CONSTRAINT khokhuyenmai_makm_foreign FOREIGN KEY (MaKM) REFERENCES KhuyenMai(MaKM) ON DELETE CASCADE');
        DB::statement('ALTER TABLE HoaDon ADD CONSTRAINT hoadon_makm_foreign FOREIGN KEY (MaKM) REFERENCES KhuyenMai(MaKM) ON DELETE SET NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE KhoKhuyenMai DROP FOREIGN KEY khokhuyenmai_makm_foreign');
        DB::statement('ALTER TABLE HoaDon DROP FOREIGN KEY hoadon_makm_foreign');
        DB::statement('ALTER TABLE KhoKhuyenMai DROP PRIMARY KEY');

        DB::statement('ALTER TABLE KhuyenMai MODIFY MaKM BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE KhoKhuyenMai MODIFY MaKM BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE HoaDon MODIFY MaKM BIGINT UNSIGNED NULL');

        DB::statement('ALTER TABLE KhoKhuyenMai ADD PRIMARY KEY (MaKM, MaKH)');
        DB::statement('ALTER TABLE KhoKhuyenMai ADD CONSTRAINT khokhuyenmai_makm_foreign FOREIGN KEY (MaKM) REFERENCES KhuyenMai(MaKM) ON DELETE CASCADE');
        DB::statement('ALTER TABLE HoaDon ADD CONSTRAINT hoadon_makm_foreign FOREIGN KEY (MaKM) REFERENCES KhuyenMai(MaKM) ON DELETE SET NULL');
    }
};
