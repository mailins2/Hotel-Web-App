<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if ($this->isVarcharSchema()) {
            return;
        }

        $khoKhuyenMaiFk = $this->getForeignKeyName('KhoKhuyenMai', 'MaKM', 'KhuyenMai', 'MaKM');
        $hoaDonFk = $this->getForeignKeyName('HoaDon', 'MaKM', 'KhuyenMai', 'MaKM');

        if ($khoKhuyenMaiFk !== null) {
            DB::statement("ALTER TABLE KhoKhuyenMai DROP FOREIGN KEY `{$khoKhuyenMaiFk}`");
        }

        if ($hoaDonFk !== null) {
            DB::statement("ALTER TABLE HoaDon DROP FOREIGN KEY `{$hoaDonFk}`");
        }

        if ($this->hasPrimaryKey('KhoKhuyenMai')) {
            DB::statement('ALTER TABLE KhoKhuyenMai DROP PRIMARY KEY');
        }

        DB::statement('ALTER TABLE KhuyenMai MODIFY MaKM VARCHAR(10) NOT NULL');
        DB::statement('ALTER TABLE KhoKhuyenMai MODIFY MaKM VARCHAR(10) NOT NULL');
        DB::statement('ALTER TABLE HoaDon MODIFY MaKM VARCHAR(10) NULL');

        if (! $this->hasPrimaryKey('KhoKhuyenMai')) {
            DB::statement('ALTER TABLE KhoKhuyenMai ADD PRIMARY KEY (MaKM, MaKH)');
        }

        if ($this->getForeignKeyName('KhoKhuyenMai', 'MaKM', 'KhuyenMai', 'MaKM') === null) {
            DB::statement('ALTER TABLE KhoKhuyenMai ADD CONSTRAINT khokhuyenmai_makm_foreign FOREIGN KEY (MaKM) REFERENCES KhuyenMai(MaKM) ON DELETE CASCADE');
        }

        if ($this->getForeignKeyName('HoaDon', 'MaKM', 'KhuyenMai', 'MaKM') === null) {
            DB::statement('ALTER TABLE HoaDon ADD CONSTRAINT hoadon_makm_foreign FOREIGN KEY (MaKM) REFERENCES KhuyenMai(MaKM) ON DELETE SET NULL');
        }
    }

    public function down(): void
    {
        if ($this->isBigIntSchema()) {
            return;
        }

        if ($this->hasNonNumericPromotionCodes()) {
            throw new RuntimeException('Cannot roll back MaKM to BIGINT because existing promotion codes contain non-numeric values.');
        }

        $khoKhuyenMaiFk = $this->getForeignKeyName('KhoKhuyenMai', 'MaKM', 'KhuyenMai', 'MaKM');
        $hoaDonFk = $this->getForeignKeyName('HoaDon', 'MaKM', 'KhuyenMai', 'MaKM');

        if ($khoKhuyenMaiFk !== null) {
            DB::statement("ALTER TABLE KhoKhuyenMai DROP FOREIGN KEY `{$khoKhuyenMaiFk}`");
        }

        if ($hoaDonFk !== null) {
            DB::statement("ALTER TABLE HoaDon DROP FOREIGN KEY `{$hoaDonFk}`");
        }

        if ($this->hasPrimaryKey('KhoKhuyenMai')) {
            DB::statement('ALTER TABLE KhoKhuyenMai DROP PRIMARY KEY');
        }

        DB::statement('ALTER TABLE KhuyenMai MODIFY MaKM BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE KhoKhuyenMai MODIFY MaKM BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE HoaDon MODIFY MaKM BIGINT UNSIGNED NULL');

        if (! $this->hasPrimaryKey('KhoKhuyenMai')) {
            DB::statement('ALTER TABLE KhoKhuyenMai ADD PRIMARY KEY (MaKM, MaKH)');
        }

        if ($this->getForeignKeyName('KhoKhuyenMai', 'MaKM', 'KhuyenMai', 'MaKM') === null) {
            DB::statement('ALTER TABLE KhoKhuyenMai ADD CONSTRAINT khokhuyenmai_makm_foreign FOREIGN KEY (MaKM) REFERENCES KhuyenMai(MaKM) ON DELETE CASCADE');
        }

        if ($this->getForeignKeyName('HoaDon', 'MaKM', 'KhuyenMai', 'MaKM') === null) {
            DB::statement('ALTER TABLE HoaDon ADD CONSTRAINT hoadon_makm_foreign FOREIGN KEY (MaKM) REFERENCES KhuyenMai(MaKM) ON DELETE SET NULL');
        }
    }

    private function isVarcharSchema(): bool
    {
        return $this->getColumnType('KhuyenMai', 'MaKM') === 'varchar'
            && $this->getColumnType('KhoKhuyenMai', 'MaKM') === 'varchar'
            && $this->getColumnType('HoaDon', 'MaKM') === 'varchar';
    }

    private function isBigIntSchema(): bool
    {
        return $this->getColumnType('KhuyenMai', 'MaKM') === 'bigint'
            && $this->getColumnType('KhoKhuyenMai', 'MaKM') === 'bigint'
            && $this->getColumnType('HoaDon', 'MaKM') === 'bigint';
    }

    private function getColumnType(string $table, string $column): ?string
    {
        $row = DB::selectOne(
            'SELECT DATA_TYPE
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?',
            [$table, $column]
        );

        return $row?->DATA_TYPE !== null ? strtolower($row->DATA_TYPE) : null;
    }

    private function hasPrimaryKey(string $table): bool
    {
        $row = DB::selectOne(
            'SELECT CONSTRAINT_NAME
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_TYPE = ?',
            [$table, 'PRIMARY KEY']
        );

        return $row !== null;
    }

    private function getForeignKeyName(
        string $table,
        string $column,
        string $referencedTable,
        string $referencedColumn
    ): ?string {
        $row = DB::selectOne(
            'SELECT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME = ?
               AND REFERENCED_COLUMN_NAME = ?
             LIMIT 1',
            [$table, $column, $referencedTable, $referencedColumn]
        );

        return $row?->CONSTRAINT_NAME;
    }

    private function hasNonNumericPromotionCodes(): bool
    {
        $khuyenMai = DB::table('KhuyenMai')
            ->whereRaw("CAST(MaKM AS CHAR) REGEXP '[^0-9]'")
            ->exists();

        $khoKhuyenMai = DB::table('KhoKhuyenMai')
            ->whereRaw("CAST(MaKM AS CHAR) REGEXP '[^0-9]'")
            ->exists();

        $hoaDon = DB::table('HoaDon')
            ->whereNotNull('MaKM')
            ->whereRaw("CAST(MaKM AS CHAR) REGEXP '[^0-9]'")
            ->exists();

        return $khuyenMai || $khoKhuyenMai || $hoaDon;
    }
};
