<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->setCccdNullable(true);
    }

    public function down(): void
    {
        $this->setCccdNullable(false);
    }

    private function setCccdNullable(bool $nullable): void
    {
        if (!Schema::hasColumn('KhachHang', 'CCCD')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE `KhachHang` MODIFY `CCCD` VARCHAR(20) ' . ($nullable ? 'NULL' : 'NOT NULL'));
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE "KhachHang" ALTER COLUMN "CCCD" ' . ($nullable ? 'DROP NOT NULL' : 'SET NOT NULL'));
            return;
        }

        if ($driver === 'sqlsrv') {
            DB::statement('ALTER TABLE [KhachHang] ALTER COLUMN [CCCD] NVARCHAR(20) ' . ($nullable ? 'NULL' : 'NOT NULL'));
        }
    }
};
