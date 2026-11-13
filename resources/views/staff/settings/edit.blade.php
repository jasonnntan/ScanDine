@extends('layouts.staff')

@section('title', 'Settings')

@section('content')
<div class="max-w-2xl mx-auto p-4 md:p-6">

    <h1 class="text-2xl font-bold text-gray-800">Settings</h1>
    <p class="text-sm text-gray-500 mb-4">Choose which charges are added to the payment receipt.</p>

    @if (session('success'))
        <p class="bg-green-100 text-green-800 p-3 rounded-xl mb-4">{{ session('success') }}</p>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded-xl mb-4 text-sm">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('staff.settings.update') }}" method="POST" class="bg-white rounded-xl shadow-md p-4 flex flex-col gap-4">
        @csrf
        @method('PUT')

        <div class="border border-gray-100 rounded-lg p-4">
            <label class="flex items-center gap-3 font-semibold text-gray-800">
                <input type="checkbox" name="sst_enabled" value="1" {{ $setting->sst_enabled ? 'checked' : '' }} class="w-5 h-5">
                Charge SST
            </label>
            <label class="block mt-3 text-sm font-semibold text-gray-700">Rate (%)
                <input type="number" name="sst_rate" step="0.01" min="0" max="100" required
                       value="{{ old('sst_rate', $setting->sst_rate) }}"
                       class="mt-1 block w-32 p-2 border border-gray-200 rounded-lg font-normal">
            </label>
        </div>

        <div class="border border-gray-100 rounded-lg p-4">
            <label class="flex items-center gap-3 font-semibold text-gray-800">
                <input type="checkbox" name="service_charge_enabled" value="1" {{ $setting->service_charge_enabled ? 'checked' : '' }} class="w-5 h-5">
                Charge service charge
            </label>
            <label class="block mt-3 text-sm font-semibold text-gray-700">Rate (%)
                <input type="number" name="service_charge_rate" step="0.01" min="0" max="100" required
                       value="{{ old('service_charge_rate', $setting->service_charge_rate) }}"
                       class="mt-1 block w-32 p-2 border border-gray-200 rounded-lg font-normal">
            </label>
        </div>

        <button type="submit" class="bg-orange-500 text-white font-semibold px-4 py-2 rounded-lg text-sm self-start">Save settings</button>
    </form>

</div>
@endsection