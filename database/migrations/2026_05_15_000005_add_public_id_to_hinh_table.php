<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('Hinh', 'public_id')) {
            Schema::table('Hinh', function (Blueprint $table) {
                $table->string('public_id', 255)->nullable()->after('Url');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('Hinh', 'public_id')) {
            Schema::table('Hinh', function (Blueprint $table) {
                $table->dropColumn('public_id');
            });
        }
    }
};
