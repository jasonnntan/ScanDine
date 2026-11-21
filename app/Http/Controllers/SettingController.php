<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'restaurant_name' => 'required|string|max:100',
            'banner_image' => 'nullable|image|max:2048',
            'sst_rate' => 'required|numeric|min:0|max:100',
            'service_charge_rate' => 'required|numeric|min:0|max:100',
        ]);

        $setting = Setting::current();

        $data = [
            'restaurant_name' => $request->restaurant_name,
            'sst_enabled' => $request->boolean('sst_enabled'),
            'sst_rate' => $request->sst_rate,
            'service_charge_enabled' => $request->boolean('service_charge_enabled'),
            'service_charge_rate' => $request->service_charge_rate,
        ];

        if ($request->hasFile('banner_image')) {
            // a new picture replaces the old one
            if ($setting->banner_image) {
                Storage::disk('public')->delete($setting->banner_image);
            }
            $data['banner_image'] = $request->file('banner_image')->store('banners', 'public');
        } elseif ($request->boolean('remove_banner') && $setting->banner_image) {
            Storage::disk('public')->delete($setting->banner_image);
            $data['banner_image'] = null;
        }

        $setting->update($data);

        return redirect()->route('staff.settings.edit')->with('success', 'Settings saved!');
    }
}
