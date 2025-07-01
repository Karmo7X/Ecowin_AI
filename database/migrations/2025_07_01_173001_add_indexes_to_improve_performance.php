<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('phone');
            $table->index('role');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('name_ar');
            $table->index('name_en');
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->index('title_ar');
            $table->index('title_en');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->index('question_ar');
            $table->index('question_en');
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('brand_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropIndex(['role']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['name_ar']);
            $table->dropIndex(['name_en']);
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->dropIndex(['title_ar']);
            $table->dropIndex(['title_en']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex(['question_ar']);
            $table->dropIndex(['question_en']);
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['brand_id']);
        });

        Schema::table('wallets', function (Blueprint $table) {
            $table->index('points');
        });

    }
};
