<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table {{ $table->table_number }} QR Slip - DineFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-slip {
                box-shadow: none !important;
                border: 2px solid #1c1917 !important;
                margin: 0 auto !important;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-stone-100 text-stone-800 antialiased flex flex-col items-center justify-center p-4 sm:p-6">

    @php
        $restaurantName = \App\Models\Setting::first()?->restaurant_name ?? 'DineFlow';
        $orderUrl = url('/order/' . $table->qr_token);
    @endphp

    {{-- Screen-Only Action Bar --}}
    <div class="no-print w-full max-w-sm flex items-center justify-between gap-2 mb-4">
        <button type="button" onclick="window.close()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-stone-200 hover:bg-stone-50 text-xs font-bold text-stone-700 transition">
            <i class="fa-solid fa-xmark text-stone-400" aria-hidden="true"></i>
            <span>Close</span>
        </button>

        <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-extrabold shadow-sm transition">
            <i class="fa-solid fa-print" aria-hidden="true"></i>
            <span>Print QR Slip</span>
        </button>
    </div>

    {{-- Printable Table Tent / Receipt Card --}}
    <div class="print-slip w-full max-w-sm bg-white rounded-3xl border border-stone-200 shadow-xl overflow-hidden text-center">

        {{-- Top Accent Strip --}}
        <div class="bg-stone-900 text-white px-6 py-4">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-orange-400">{{ $restaurantName }}</p>
            <h1 class="text-3xl font-extrabold tracking-tight mt-1">Table {{ $table->table_number }}</h1>
        </div>

        <div class="p-6 sm:p-7 space-y-5">
            <div>
                <p class="text-sm font-extrabold text-stone-900">Scan to View Menu & Order</p>
                <p class="text-xs text-stone-500 mt-0.5">No app download required — order straight from your phone.</p>
            </div>

            {{-- Crisp High-Contrast QR Code --}}
            <div class="inline-flex items-center justify-center p-4 bg-white border-2 border-stone-900 rounded-2xl">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(210)->margin(1)->generate($orderUrl) !!}
            </div>

            {{-- 3-Step Quick Guide --}}
            <div class="grid grid-cols-3 gap-2 pt-1 text-center">
                <div class="bg-stone-50 rounded-xl p-2.5 border border-stone-100">
                    <span class="inline-flex w-5 h-5 rounded-full bg-orange-100 text-orange-700 font-extrabold text-[11px] items-center justify-center mb-1">1</span>
                    <p class="text-[11px] font-bold text-stone-700 leading-tight">Scan QR</p>
                </div>
                <div class="bg-stone-50 rounded-xl p-2.5 border border-stone-100">
                    <span class="inline-flex w-5 h-5 rounded-full bg-orange-100 text-orange-700 font-extrabold text-[11px] items-center justify-center mb-1">2</span>
                    <p class="text-[11px] font-bold text-stone-700 leading-tight">Pick Food</p>
                </div>
                <div class="bg-stone-50 rounded-xl p-2.5 border border-stone-100">
                    <span class="inline-flex w-5 h-5 rounded-full bg-orange-100 text-orange-700 font-extrabold text-[11px] items-center justify-center mb-1">3</span>
                    <p class="text-[11px] font-bold text-stone-700 leading-tight">Send Order</p>
                </div>
            </div>

            {{-- Fallback Link & Timestamp --}}
            <div class="pt-3 border-t border-dashed border-stone-200 space-y-1">
                <p class="text-[10px] font-mono text-stone-400 break-all">{{ $orderUrl }}</p>
                <p class="text-[10px] font-medium text-stone-400">Session generated {{ now()->format('d M Y, h:i A') }}</p>
            </div>
        </div>

    </div>

</body>
</html>
