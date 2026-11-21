<?php

namespace App\Http\Controllers;

use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TableController extends Controller
{
    public function index()
    {
        $tables = RestaurantTable::all();

        return view('staff.tables.index', [
            'tables' => $tables,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:100',
        ]);

        $count = (int) $request->count;

        // continue numbering after the highest table number
        $next = (int) RestaurantTable::pluck('table_number')
            ->map(function ($number) {
                return (int) $number;
            })
            ->max() + 1;

        for ($i = 0; $i < $count; $i++) {
            RestaurantTable::create([
                'table_number' => $next + $i,
                'qr_token' => Str::random(20),
                'is_occupied' => false,
            ]);
        }

        $last = $next + $count - 1;
        $message = $count === 1
            ? 'Table ' . $next . ' added!'
            : 'Tables ' . $next . ' to ' . $last . ' added!';

        return redirect()->route('staff.tables.index')->with('success', $message);
    }

    public function openTable($id)
    {
        $table = RestaurantTable::findOrFail($id);

        $table->update([
            'qr_token' => Str::random(20),
            'is_occupied' => true,
        ]);

        return redirect()->route('staff.tables.index')->with('success', 'Table ' . $table->table_number . ' opened! New QR: ' . $table->qr_token);
    }

    public function closeTable($id)
    {
        $table = RestaurantTable::findOrFail($id);

        $table->update([
            'is_occupied' => false,
            'qr_token' => Str::random(20),
        ]);

        return redirect()->route('staff.tables.index')->with('success', 'Table ' . $table->table_number . ' closed.');
    }

    public function printQr($id)
    {
        $table = RestaurantTable::findOrFail($id);

        return view('staff.tables.print', [
            'table' => $table,
        ]);
    }
        public function destroy($id)
    {
        $table = RestaurantTable::findOrFail($id);

        if ($table->is_occupied) {
            return redirect()->route('staff.tables.index')->with('error', 'Close Table ' . $table->table_number . ' before removing it.');
        }

        if (\App\Models\CustomerOrder::where('restaurant_table_id', $table->id)->exists()) {
            return redirect()->route('staff.tables.index')->with('error', 'Table ' . $table->table_number . ' has past orders, so it cannot be removed.');
        }

        $table->delete();

        return redirect()->route('staff.tables.index')->with('success', 'Table ' . $table->table_number . ' removed.');
    }
}
