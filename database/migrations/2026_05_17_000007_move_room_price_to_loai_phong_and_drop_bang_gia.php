<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('KhuyenMai')) {
            if (!Schema::hasColumn('KhuyenMai', 'LoaiKM')) {
                Schema::table('KhuyenMai', function (Blueprint $table) {
                    $table->integer('LoaiKM')->default(0)->after('PhanTramGiamGia');
                });
            } else {
                DB::statement('ALTER TABLE KhuyenMai MODIFY LoaiKM INT NOT NULL DEFAULT 0');
            }
        }

        if (Schema::hasTable('LoaiPhong')) {
            Schema::table('LoaiPhong', function (Blueprint $table) {
                if (!Schema::hasColumn('LoaiPhong', 'GiaPhong')) {
                    $table->decimal('GiaPhong', 18, 2)->default(0)->after('TreEm');
                }

                if (!Schema::hasColumn('LoaiPhong', 'MaKM')) {
                    $table->string('MaKM', 10)->nullable()->after('GiaPhong');
                }
            });

            if (Schema::hasTable('BangGia')) {
                DB::statement("
                    UPDATE LoaiPhong lp
                    LEFT JOIN (
                        SELECT
                            MaLoaiPhong,
                            COALESCE(
                                MAX(CASE WHEN Mua = 1 THEN GiaPhong END),
                                MIN(GiaPhong)
                            ) AS GiaPhong
                        FROM BangGia
                        GROUP BY MaLoaiPhong
                    ) bg ON bg.MaLoaiPhong = lp.MaLoaiPhong
                    SET lp.GiaPhong = COALESCE(bg.GiaPhong, lp.GiaPhong, 0)
                ");
            }

            DB::statement('ALTER TABLE LoaiPhong MODIFY GiaPhong DECIMAL(18,2) NOT NULL');

            if (!$this->foreignKeyExists('LoaiPhong', 'loaiphong_makm_foreign')) {
                Schema::table('LoaiPhong', function (Blueprint $table) {
                    $table->foreign('MaKM', 'loaiphong_makm_foreign')
                        ->references('MaKM')
                        ->on('KhuyenMai')
                        ->nullOnDelete();
                });
            }
        }

        Schema::dropIfExists('BangGia');
    }

    public function down(): void
    {
        if (!Schema::hasTable('BangGia')) {
            Schema::create('BangGia', function (Blueprint $table) {
                $table->unsignedBigInteger('MaLoaiPhong');
                $table->integer('Mua');
                $table->decimal('GiaPhong', 18, 2);
                $table->primary(['MaLoaiPhong', 'Mua']);
                $table->foreign('MaLoaiPhong')
                    ->references('MaLoaiPhong')
                    ->on('LoaiPhong')
                    ->cascadeOnDelete();
            });

            if (Schema::hasTable('LoaiPhong') && Schema::hasColumn('LoaiPhong', 'GiaPhong')) {
                DB::statement('
                    INSERT INTO BangGia (MaLoaiPhong, Mua, GiaPhong)
                    SELECT MaLoaiPhong, 1, GiaPhong
                    FROM LoaiPhong
                ');
            }
        }

        if (Schema::hasTable('LoaiPhong')) {
            $this->dropForeignIfExists('LoaiPhong', 'loaiphong_makm_foreign');

            Schema::table('LoaiPhong', function (Blueprint $table) {
                if (Schema::hasColumn('LoaiPhong', 'MaKM')) {
                    $table->dropColumn('MaKM');
                }

                if (Schema::hasColumn('LoaiPhong', 'GiaPhong')) {
                    $table->dropColumn('GiaPhong');
                }
            });
        }
    }

    private function foreignKeyExists(string $table, string $foreignKey): bool
    {
        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $foreignKey)
            ->exists();
    }

    private function dropForeignIfExists(string $table, string $foreignKey): void
    {
        if ($this->foreignKeyExists($table, $foreignKey)) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$foreignKey}`");
        }
    }
};
