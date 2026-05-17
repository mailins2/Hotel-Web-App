<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('TaiKhoan', function (Blueprint $table) {
            if (!Schema::hasColumn('TaiKhoan', 'MaKH')) {
                $table->unsignedBigInteger('MaKH')->nullable()->after('TrangThai');
            }

            if (!Schema::hasColumn('TaiKhoan', 'MaNV')) {
                $table->unsignedBigInteger('MaNV')->nullable()->after('MaKH');
            }
        });

        Schema::table('NhanVien', function (Blueprint $table) {
            if (!Schema::hasColumn('NhanVien', 'ChucVu')) {
                $table->integer('ChucVu')->nullable()->after('TenNV');
            }
        });

        if (Schema::hasColumn('KhachHang', 'MaTK')) {
            DB::table('TaiKhoan')
                ->join('KhachHang', 'KhachHang.MaTK', '=', 'TaiKhoan.MaTK')
                ->whereNull('TaiKhoan.MaKH')
                ->update(['TaiKhoan.MaKH' => DB::raw('KhachHang.MaKH')]);
        }

        if (Schema::hasColumn('NhanVien', 'MaTK')) {
            DB::table('TaiKhoan')
                ->join('NhanVien', 'NhanVien.MaTK', '=', 'TaiKhoan.MaTK')
                ->whereNull('TaiKhoan.MaNV')
                ->update(['TaiKhoan.MaNV' => DB::raw('NhanVien.MaNV')]);
        }

        $this->dropForeignIfExists('KhachHang', 'MaTK');
        $this->dropForeignIfExists('NhanVien', 'MaTK');
        $this->dropIndexesForColumn('KhachHang', 'MaTK');
        $this->dropIndexesForColumn('NhanVien', 'MaTK');

        Schema::table('KhachHang', function (Blueprint $table) {
            if (Schema::hasColumn('KhachHang', 'MaTK')) {
                $table->dropColumn('MaTK');
            }
        });

        Schema::table('NhanVien', function (Blueprint $table) {
            if (Schema::hasColumn('NhanVien', 'MaTK')) {
                $table->dropColumn('MaTK');
            }
        });

        Schema::table('TaiKhoan', function (Blueprint $table) {
            if (!$this->indexExists('TaiKhoan', 'uq_taikhoan_makh')) {
                $table->unique('MaKH', 'uq_taikhoan_makh');
            }

            if (!$this->indexExists('TaiKhoan', 'uq_taikhoan_manv')) {
                $table->unique('MaNV', 'uq_taikhoan_manv');
            }

            if (!$this->foreignKeyExists('TaiKhoan', 'fk_taikhoan_khachhang')) {
                $table->foreign('MaKH', 'fk_taikhoan_khachhang')
                    ->references('MaKH')
                    ->on('KhachHang')
                    ->nullOnDelete();
            }

            if (!$this->foreignKeyExists('TaiKhoan', 'fk_taikhoan_nhanvien')) {
                $table->foreign('MaNV', 'fk_taikhoan_nhanvien')
                    ->references('MaNV')
                    ->on('NhanVien')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        $this->dropForeignIfExists('TaiKhoan', 'MaKH');
        $this->dropForeignIfExists('TaiKhoan', 'MaNV');
        $this->dropIndexIfExists('TaiKhoan', 'uq_taikhoan_makh');
        $this->dropIndexIfExists('TaiKhoan', 'uq_taikhoan_manv');

        Schema::table('KhachHang', function (Blueprint $table) {
            if (!Schema::hasColumn('KhachHang', 'MaTK')) {
                $table->unsignedBigInteger('MaTK')->nullable()->unique()->after('MaKH');
            }
        });

        Schema::table('NhanVien', function (Blueprint $table) {
            if (!Schema::hasColumn('NhanVien', 'MaTK')) {
                $table->unsignedBigInteger('MaTK')->nullable()->unique()->after('TenNV');
            }
        });

        if (Schema::hasColumn('TaiKhoan', 'MaKH')) {
            DB::table('KhachHang')
                ->join('TaiKhoan', 'TaiKhoan.MaKH', '=', 'KhachHang.MaKH')
                ->whereNull('KhachHang.MaTK')
                ->update(['KhachHang.MaTK' => DB::raw('TaiKhoan.MaTK')]);
        }

        if (Schema::hasColumn('TaiKhoan', 'MaNV')) {
            DB::table('NhanVien')
                ->join('TaiKhoan', 'TaiKhoan.MaNV', '=', 'NhanVien.MaNV')
                ->whereNull('NhanVien.MaTK')
                ->update(['NhanVien.MaTK' => DB::raw('TaiKhoan.MaTK')]);
        }

        Schema::table('KhachHang', function (Blueprint $table) {
            if (!$this->foreignKeyExists('KhachHang', 'khachhang_matk_foreign')) {
                $table->foreign('MaTK')->references('MaTK')->on('TaiKhoan')->nullOnDelete();
            }
        });

        Schema::table('NhanVien', function (Blueprint $table) {
            if (!$this->foreignKeyExists('NhanVien', 'nhanvien_matk_foreign')) {
                $table->foreign('MaTK')->references('MaTK')->on('TaiKhoan')->cascadeOnDelete();
            }

            if (Schema::hasColumn('NhanVien', 'ChucVu')) {
                $table->dropColumn('ChucVu');
            }
        });

        Schema::table('TaiKhoan', function (Blueprint $table) {
            if (Schema::hasColumn('TaiKhoan', 'MaKH')) {
                $table->dropColumn('MaKH');
            }

            if (Schema::hasColumn('TaiKhoan', 'MaNV')) {
                $table->dropColumn('MaNV');
            }
        });
    }

    private function dropForeignIfExists(string $table, string $column): void
    {
        $foreignKey = $this->foreignKeyForColumn($table, $column);

        if ($foreignKey === null) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($foreignKey) {
            $blueprint->dropForeign($foreignKey);
        });
    }

    private function dropIndexIfExists(string $table, string $index): void
    {
        if (!$this->indexExists($table, $index)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($index) {
            $blueprint->dropIndex($index);
        });
    }

    private function dropIndexesForColumn(string $table, string $column): void
    {
        $indexes = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::raw('DATABASE()'))
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->where('INDEX_NAME', '<>', 'PRIMARY')
            ->distinct()
            ->pluck('INDEX_NAME');

        foreach ($indexes as $index) {
            Schema::table($table, function (Blueprint $blueprint) use ($index) {
                $blueprint->dropIndex($index);
            });
        }
    }

    private function foreignKeyForColumn(string $table, string $column): ?string
    {
        $row = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->select('CONSTRAINT_NAME')
            ->where('TABLE_SCHEMA', DB::raw('DATABASE()'))
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->first();

        return $row?->CONSTRAINT_NAME;
    }

    private function foreignKeyExists(string $table, string $foreignKey): bool
    {
        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('TABLE_SCHEMA', DB::raw('DATABASE()'))
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $foreignKey)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();
    }

    private function indexExists(string $table, string $index): bool
    {
        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::raw('DATABASE()'))
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->exists();
    }
};
