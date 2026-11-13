<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        return view('staff.settings.edit', [
            'setting' => Setting::current(),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'sst_rate' => 'required|numeric|min:0|max:100',
            'service_charge_rate' => 'required|numeric|min:0|max:100',
        ]);

        Setting::current()->update([
            'sst_enabled' => $request->boolean('sst_enabled'),
            'sst_rate' => $request->sst_rate,
            'service_charge_enabled' => $request->boolean('service_charge_enabled'),
            'service_charge_rate' => $request->service_charge_rate,
        ]);

        return redirect()->route('staff.settings.edit')->with('success', 'Settings saved!');
    }
}
