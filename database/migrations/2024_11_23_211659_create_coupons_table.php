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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->integer('discount_value');
            $table->integer('price');
            $table->dateTime('expires_at')->nullable(); // when it's no longer usable
            $table->dateTime('redeemed_at')->nullable(); // when user redeems it
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->nullable();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
