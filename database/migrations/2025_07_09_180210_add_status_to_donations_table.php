<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            // إضافة عمود 'status' كـ ENUM مع القيم المحددة
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending')->after('description');
            // يمكنك تغيير after('description') لوضع العمود في مكان آخر إذا أردت.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            // حذف عمود 'status' في حالة التراجع عن الـ migration
            $table->dropColumn('status');
        });
    }
};
