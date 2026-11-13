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
    Schema::create('settings', function (Blueprint $table) {
        $table->id();
        $table->boolean('sst_enabled')->default(false);
        $table->decimal('sst_rate', 5, 2)->default(6);
        $table->boolean('service_charge_enabled')->default(false);
        $table->decimal('service_charge_rate', 5, 2)->default(10);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('settings');
}
};
