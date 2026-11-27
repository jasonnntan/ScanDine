@extends('layouts.staff')

@section('title', 'Settings')

@section('content')
<div class="max-w-3xl mx-auto p-4 md:p-6 space-y-5">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Restaurant Settings</h1>
        <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
            Customize your digital menu branding and receipt tax/service charges.
        </p>
    </div>

    <form action="{{ route('staff.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Branding Card --}}
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-5 sm:p-6 space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-stone-100">
                <div class="w-8 h-8 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-store" aria-hidden="true"></i>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-stone-900">Branding & Menu Header</h2>
                    <p class="text-xs text-stone-400">Displayed at the top of the customer QR ordering page</p>
                </div>
            </div>

            <label class="block text-xs font-bold text-stone-600 uppercase tracking-wide">Restaurant Name
                <input type="text" name="restaurant_name" required maxlength="100"
                       value="{{ old('restaurant_name', $setting->restaurant_name) }}"
                       class="mt-1.5 w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-semibold text-stone-900 focus:outline-none">
            </label>

            <div>
                <p class="text-xs font-bold text-stone-600 uppercase tracking-wide mb-2">Menu Banner Image</p>

                @if ($setting->banner_image)
                    <div class="relative rounded-xl overflow-hidden border border-stone-200 mb-3">
                        <img id="bannerPreviewImg" src="{{ asset('storage/' . $setting->banner_image) }}" alt="Current Banner" class="w-full h-40 object-cover">
                    </div>

                    <label class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-red-50/70 border border-red-200/80 text-xs font-semibold text-red-700 mb-3 cursor-pointer">
                        <input type="checkbox" name="remove_banner" value="1" class="rounded text-red-600 focus:ring-red-500">
                        <span>Remove the current banner image</span>
                    </label>
                @else
                    <div id="bannerPreviewWrap" class="hidden relative rounded-xl overflow-hidden border border-stone-200 mb-3">
                        <img id="bannerPreviewImg" src="" alt="Banner Preview" class="w-full h-40 object-cover">
                    </div>
                @endif

                <input type="file" name="banner_image" accept="image/*" onchange="previewBanner(this)"
                       class="w-full text-xs text-stone-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                <p class="text-[11px] text-stone-400 mt-1.5">Wide landscape photos work best (max 2 MB). Leave empty to keep your current banner.</p>
            </div>
        </div>

        {{-- Tax & Service Charges Card --}}
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-5 sm:p-6 space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-stone-100">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-percent" aria-hidden="true"></i>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-stone-900">Receipt Taxes & Charges</h2>
                    <p class="text-xs text-stone-400">Automatically calculated on customer receipts</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- SST Box --}}
                <div class="border border-stone-200/80 rounded-2xl p-4 bg-stone-50/50 flex flex-col justify-between gap-3">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="font-bold text-sm text-stone-900">SST (Sales & Service Tax)</p>
                            <p class="text-[11px] text-stone-400">Apply government tax to bill</p>
                        </div>

                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="sstEnabled" name="sst_enabled" value="1"
                                   {{ $setting->sst_enabled ? 'checked' : '' }}
                                   onchange="updateChargePreview()"
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-stone-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                        </label>
                    </div>

                    <label class="block text-xs font-bold text-stone-600 uppercase tracking-wide">SST Rate (%)
                        <input type="number" id="sstRate" name="sst_rate" step="0.01" min="0" max="100" required
                               value="{{ old('sst_rate', $setting->sst_rate) }}"
                               oninput="updateChargePreview()"
                               class="mt-1 w-full px-3 py-2 bg-white border border-stone-200 focus:border-orange-500 rounded-xl text-sm font-bold text-stone-900 focus:outline-none">
                    </label>
                </div>

                {{-- Service Charge Box --}}
                <div class="border border-stone-200/80 rounded-2xl p-4 bg-stone-50/50 flex flex-col justify-between gap-3">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="font-bold text-sm text-stone-900">Service Charge</p>
                            <p class="text-[11px] text-stone-400">Restaurant dine-in service fee</p>
                        </div>

                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="scEnabled" name="service_charge_enabled" value="1"
                                   {{ $setting->service_charge_enabled ? 'checked' : '' }}
                                   onchange="updateChargePreview()"
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-stone-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                        </label>
                    </div>

                    <label class="block text-xs font-bold text-stone-600 uppercase tracking-wide">Service Charge Rate (%)
                        <input type="number" id="scRate" name="service_charge_rate" step="0.01" min="0" max="100" required
                               value="{{ old('service_charge_rate', $setting->service_charge_rate) }}"
                               oninput="updateChargePreview()"
                               class="mt-1 w-full px-3 py-2 bg-white border border-stone-200 focus:border-orange-500 rounded-xl text-sm font-bold text-stone-900 focus:outline-none">
                    </label>
                </div>
            </div>

            {{-- Live Sample Receipt Preview --}}
            <div class="bg-stone-50 border border-dashed border-stone-200 rounded-xl p-3.5 text-xs space-y-1.5">
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-stone-400 mb-1">Live Sample Receipt (on RM 100.00 subtotal)</p>
                <div class="flex justify-between text-stone-600">
                    <span>Food Subtotal</span>
                    <span class="font-semibold tabular-nums">RM 100.00</span>
                </div>
                <div id="previewScRow" class="flex justify-between text-stone-600">
                    <span>Service Charge (<span id="previewScPct">0</span>%)</span>
                    <span id="previewScAmt" class="font-semibold tabular-nums">RM 0.00</span>
                </div>
                <div id="previewSstRow" class="flex justify-between text-stone-600">
                    <span>SST (<span id="previewSstPct">0</span>%)</span>
                    <span id="previewSstAmt" class="font-semibold tabular-nums">RM 0.00</span>
                </div>
                <div class="flex justify-between font-extrabold text-stone-900 pt-1.5 border-t border-stone-200 text-sm">
                    <span>Sample Grand Total</span>
                    <span id="previewGrandTotal" class="tabular-nums text-orange-600">RM 100.00</span>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white font-extrabold px-6 py-3 rounded-xl text-sm shadow-sm transition">
                <i class="fa-solid fa-check" aria-hidden="true"></i>
                <span>Save Settings</span>
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    function previewBanner(input) {
        if (!input.files || !input.files[0]) { return; }
        var reader = new FileReader();
        reader.onload = function (e) {
            var img = document.getElementById('bannerPreviewImg');
            var wrap = document.getElementById('bannerPreviewWrap');
            if (img) {
                img.src = e.target.result;
            }
            if (wrap) {
                wrap.classList.remove('hidden');
            }
        };
        reader.readAsDataURL(input.files[0]);
    }

    function updateChargePreview() {
        var subtotal = 100;
        var sstOn = document.getElementById('sstEnabled').checked;
        var scOn = document.getElementById('scEnabled').checked;
        var sstRate = parseFloat(document.getElementById('sstRate').value) || 0;
        var scRate = parseFloat(document.getElementById('scRate').value) || 0;

        document.getElementById('sstRate').classList.toggle('opacity-50', !sstOn);
        document.getElementById('scRate').classList.toggle('opacity-50', !scOn);

        var scAmt = scOn ? subtotal * (scRate / 100) : 0;
        var sstAmt = sstOn ? subtotal * (sstRate / 100) : 0;
        var total = subtotal + scAmt + sstAmt;

        document.getElementById('previewScRow').classList.toggle('opacity-40', !scOn);
        document.getElementById('previewSstRow').classList.toggle('opacity-40', !sstOn);

        document.getElementById('previewScPct').textContent = scOn ? scRate.toFixed(2) : '0';
        document.getElementById('previewScAmt').textContent = 'RM ' + scAmt.toFixed(2);

        document.getElementById('previewSstPct').textContent = sstOn ? sstRate.toFixed(2) : '0';
        document.getElementById('previewSstAmt').textContent = 'RM ' + sstAmt.toFixed(2);

        document.getElementById('previewGrandTotal').textContent = 'RM ' + total.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', updateChargePreview);
</script>
@endpush
