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
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('status_accept')->default(0)->after('orderType');
            $table->boolean('status_cook')->default(0)->after('status_accept');
            $table->boolean('status_ready')->default(0)->after('status_cook');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('status_accept');
            $table->dropColumn('status_cook');
        });
    }
};
