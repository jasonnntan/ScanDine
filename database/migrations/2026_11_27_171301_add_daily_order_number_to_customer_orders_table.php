<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->unsignedInteger('daily_order_number')->default(1)->after('id');
        });

        // Backfill existing orders so each date starts from #1
        $ordersByDate = DB::table('customer_orders')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get(['id', 'created_at'])
            ->groupBy(fn ($row) => Carbon::parse($row->created_at)->toDateString());

        foreach ($ordersByDate as $rows) {
            $number = 1;
            foreach ($rows as $row) {
                DB::table('customer_orders')
                    ->where('id', $row->id)
                    ->update(['daily_order_number' => $number++]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dropColumn('daily_order_number');
        });
    }
};
