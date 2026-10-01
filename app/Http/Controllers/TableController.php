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
            'qr_token' => \Illuminate\Support\Str::random(20),
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
}
