<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('customer_orders', function (Blueprint $table) {
        $table->timestamp('preparing_at')->nullable();
        $table->timestamp('ready_at')->nullable();
        $table->timestamp('served_at')->nullable();
        $table->timestamp('completed_at')->nullable();
        $table->timestamp('paid_at')->nullable();
    });
}

public function down(): void
{
    Schema::table('customer_orders', function (Blueprint $table) {
        $table->dropColumn(['preparing_at', 'ready_at', 'served_at', 'completed_at', 'paid_at']);
    });
}

};
