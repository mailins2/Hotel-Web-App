<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('SuDungDichVu', 'MaCTDP')) {
            Schema::table('SuDungDichVu', function (Blueprint $table) {
                $table->unsignedBigInteger('MaCTDP')->nullable()->after('MaSuDung');
            });
        }

        $this->dropForeignKeyIfExists('SuDungDichVu', 'MaCTDP');
        $this->modifyColumnToMatch('SuDungDichVu', 'MaCTDP', 'ChiTietDatPhong', 'MaCTDP', true);

        if (Schema::hasColumn('SuDungDichVu', 'MaDatPhong')) {
            DB::statement(<<<'SQL'
                UPDATE SuDungDichVu sdv
                JOIN (
                    SELECT MaDatPhong, MIN(MaCTDP) AS MaCTDP
                    FROM ChiTietDatPhong
                    GROUP BY MaDatPhong
                ) ctdp ON ctdp.MaDatPhong = sdv.MaDatPhong
                SET sdv.MaCTDP = ctdp.MaCTDP
                WHERE sdv.MaCTDP IS NULL
            SQL);
        }

        if (DB::table('SuDungDichVu')->whereNull('MaCTDP')->exists()) {
            throw new RuntimeException('Khong the chuyen SuDungDichVu.MaDatPhong sang MaCTDP vi co dong khong tim thay ChiTietDatPhong tuong ung.');
        }

        $this->dropForeignKeyIfExists('SuDungDichVu', 'MaDatPhong');

        if (Schema::hasColumn('SuDungDichVu', 'MaDatPhong')) {
            Schema::table('SuDungDichVu', function (Blueprint $table) {
                $table->dropColumn('MaDatPhong');
            });
        }

        $this->modifyColumnToMatch('SuDungDichVu', 'MaCTDP', 'ChiTietDatPhong', 'MaCTDP', false);

        if (!$this->foreignKeyExists('SuDungDichVu', 'MaCTDP')) {
            Schema::table('SuDungDichVu', function (Blueprint $table) {
                $table->foreign('MaCTDP')
                    ->references('MaCTDP')
                    ->on('ChiTietDatPhong')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('SuDungDichVu', 'MaDatPhong')) {
            Schema::table('SuDungDichVu', function (Blueprint $table) {
                $table->unsignedBigInteger('MaDatPhong')->nullable()->after('MaSuDung');
            });
        }

        $this->dropForeignKeyIfExists('SuDungDichVu', 'MaDatPhong');
        $this->modifyColumnToMatch('SuDungDichVu', 'MaDatPhong', 'DatPhong', 'MaDatPhong', true);

        if (Schema::hasColumn('SuDungDichVu', 'MaCTDP')) {
            DB::statement(<<<'SQL'
                UPDATE SuDungDichVu sdv
                JOIN ChiTietDatPhong ctdp ON ctdp.MaCTDP = sdv.MaCTDP
                SET sdv.MaDatPhong = ctdp.MaDatPhong
                WHERE sdv.MaDatPhong IS NULL
            SQL);
        }

        if (DB::table('SuDungDichVu')->whereNull('MaDatPhong')->exists()) {
            throw new RuntimeException('Khong the rollback SuDungDichVu.MaCTDP sang MaDatPhong vi co dong khong tim thay ChiTietDatPhong tuong ung.');
        }

        $this->dropForeignKeyIfExists('SuDungDichVu', 'MaCTDP');

        if (Schema::hasColumn('SuDungDichVu', 'MaCTDP')) {
            Schema::table('SuDungDichVu', function (Blueprint $table) {
                $table->dropColumn('MaCTDP');
            });
        }

        $this->modifyColumnToMatch('SuDungDichVu', 'MaDatPhong', 'DatPhong', 'MaDatPhong', false);

        if (!$this->foreignKeyExists('SuDungDichVu', 'MaDatPhong')) {
            Schema::table('SuDungDichVu', function (Blueprint $table) {
                $table->foreign('MaDatPhong')
                    ->references('MaDatPhong')
                    ->on('DatPhong')
                    ->onDelete('cascade');
            });
        }
    }

    private function dropForeignKeyIfExists(string $table, string $column): void
    {
        $constraintName = $this->foreignKeyName($table, $column);

        if ($constraintName) {
            DB::statement(sprintf(
                'ALTER TABLE `%s` DROP FOREIGN KEY `%s`',
                str_replace('`', '``', $table),
                str_replace('`', '``', $constraintName)
            ));
        }
    }

    private function foreignKeyExists(string $table, string $column): bool
    {
        return $this->foreignKeyName($table, $column) !== null;
    }

    private function foreignKeyName(string $table, string $column): ?string
    {
        $rows = DB::select(<<<'SQL'
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND COLUMN_NAME = ?
                AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        SQL, [$table, $column]);

        return $rows[0]->CONSTRAINT_NAME ?? null;
    }

    private function modifyColumnToMatch(
        string $table,
        string $column,
        string $referencedTable,
        string $referencedColumn,
        bool $nullable
    ): void {
        $columnType = $this->columnType($referencedTable, $referencedColumn);

        DB::statement(sprintf(
            'ALTER TABLE `%s` MODIFY `%s` %s %s',
            str_replace('`', '``', $table),
            str_replace('`', '``', $column),
            $columnType,
            $nullable ? 'NULL' : 'NOT NULL'
        ));
    }

    private function columnType(string $table, string $column): string
    {
        $rows = DB::select(<<<'SQL'
            SELECT COLUMN_TYPE
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND COLUMN_NAME = ?
            LIMIT 1
        SQL, [$table, $column]);

        if (!$rows) {
            throw new RuntimeException("Khong tim thay cot {$table}.{$column} de doi kieu du lieu.");
        }

        return $rows[0]->COLUMN_TYPE;
    }
};
