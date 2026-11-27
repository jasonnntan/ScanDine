@extends('layouts.staff')

@section('title', 'Tables')

@section('content')
<div class="max-w-7xl mx-auto p-4 md:p-6 space-y-5">

    @php
        $canAddTables = true;     // staff, manager and owner can all add tables
        $canRemoveTables = true;  // and remove a table that has never had an order
        $nextTableNumber = (int) $tables->map(function ($t) { return (int) $t->table_number; })->max() + 1;
        $occupiedCount = $tables->where('is_occupied', true)->count();
        $freeCount = $tables->count() - $occupiedCount;
    @endphp

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Restaurant Tables</h1>
            <div class="flex flex-wrap items-center gap-2 mt-1 text-xs">
                <span class="font-semibold text-stone-500">{{ $tables->count() }} total</span>
                <span class="text-stone-300">•</span>
                <span class="inline-flex items-center gap-1.5 font-bold text-red-700 bg-red-50 px-2.5 py-0.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                    {{ $occupiedCount }} Occupied
                </span>
                <span class="inline-flex items-center gap-1.5 font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    {{ $freeCount }} Free
                </span>
            </div>
        </div>

        @if ($canAddTables)
            <button type="button" onclick="openAddTableModal()" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-sm transition self-start sm:self-auto">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                <span>Add Tables</span>
            </button>
        @endif
    </div>

    {{-- Search & Status Filter Toolbar --}}
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-3.5 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <i class="fa-solid fa-magnifying-glass text-stone-400 text-xs absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" aria-hidden="true"></i>
            <input type="text" id="tableSearchInput" oninput="filterTables()" placeholder="Search table number..."
                   class="w-full pl-9 pr-3 py-2 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm text-stone-800 focus:outline-none">
        </div>

        <div class="flex items-center gap-1.5 bg-stone-100 p-1 rounded-xl self-start sm:self-auto">
            <button type="button" onclick="setTableFilter('all', this)" class="table-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-stone-900 shadow-xs transition">
                All ({{ $tables->count() }})
            </button>
            <button type="button" onclick="setTableFilter('occupied', this)" class="table-filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-stone-600 hover:text-stone-900 transition">
                Occupied ({{ $occupiedCount }})
            </button>
            <button type="button" onclick="setTableFilter('free', this)" class="table-filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-stone-600 hover:text-stone-900 transition">
                Free ({{ $freeCount }})
            </button>
        </div>
    </div>

    @if ($tables->isEmpty())
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-12 text-center">
            <div class="w-12 h-12 rounded-full bg-stone-100 text-stone-400 flex items-center justify-center mx-auto mb-3 text-lg">
                <i class="fa-solid fa-chair" aria-hidden="true"></i>
            </div>
            <p class="font-bold text-stone-800">No restaurant tables yet</p>
            <p class="text-xs text-stone-400 mt-1">Click "Add Tables" above to generate QR ordering tables.</p>
        </div>
    @else
        <div id="emptyTableFilter" class="hidden bg-white rounded-2xl border border-stone-200/80 shadow-sm p-10 text-center">
            <p class="font-bold text-stone-700">No matching tables found</p>
            <p class="text-xs text-stone-400 mt-1">Try clearing your search or switching the status filter.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach ($tables as $table)
                @php
                    $orderUrl = url('/order/' . $table->qr_token);
                @endphp

                <div class="table-card bg-white rounded-2xl border border-stone-200/80 shadow-sm overflow-hidden flex flex-col justify-between transition hover:shadow-md"
                     data-number="{{ strtolower($table->table_number) }}"
                     data-status="{{ $table->is_occupied ? 'occupied' : 'free' }}">

                    {{-- Status Accent Bar --}}
                    <div class="h-1.5 w-full {{ $table->is_occupied ? 'bg-red-500' : 'bg-emerald-500' }}"></div>

                    <div class="p-5 flex-1 flex flex-col justify-between text-center">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="text-xs font-extrabold uppercase tracking-wider text-stone-400">Table</span>
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full {{ $table->is_occupied ? 'bg-red-50 text-red-700 border border-red-200/80' : 'bg-emerald-50 text-emerald-700 border border-emerald-200/80' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $table->is_occupied ? 'bg-red-500' : 'bg-emerald-500' }}"></span>
                                    {{ $table->is_occupied ? 'Occupied' : 'Free' }}
                                </span>
                            </div>

                            <h2 class="text-3xl font-extrabold text-stone-900 tracking-tight mb-3">
                                {{ $table->table_number }}
                            </h2>

                            @if ($table->is_occupied)
                                <div class="inline-flex items-center justify-center p-3 bg-white border border-stone-200 rounded-2xl shadow-xs mb-3">
                                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(140)->generate($orderUrl) !!}
                                </div>
                            @else
                                <div class="my-6 py-4 px-3 rounded-2xl bg-stone-50 border border-dashed border-stone-200 text-stone-400">
                                    <i class="fa-solid fa-qrcode text-2xl mb-1.5 text-stone-300" aria-hidden="true"></i>
                                    <p class="text-xs font-medium">Open table to generate active QR session</p>
                                </div>
                            @endif
                        </div>

                        <div class="pt-2 space-y-2">
                            @if ($table->is_occupied)
                                <div class="grid grid-cols-2 gap-2">
                                    <a href="{{ route('staff.tables.print', $table->id) }}" target="_blank"
                                       class="flex items-center justify-center gap-1.5 border border-stone-200 hover:bg-stone-50 text-stone-700 font-bold px-3 py-2 rounded-xl text-xs transition">
                                        <i class="fa-solid fa-print text-stone-400" aria-hidden="true"></i>
                                        <span>Print QR</span>
                                    </a>

                                    <button type="button" onclick="copyOrderLink('{{ $orderUrl }}', this)"
                                            class="flex items-center justify-center gap-1.5 border border-stone-200 hover:bg-stone-50 text-stone-700 font-bold px-3 py-2 rounded-xl text-xs transition">
                                        <i class="fa-regular fa-copy text-stone-400" aria-hidden="true"></i>
                                        <span>Copy Link</span>
                                    </button>
                                </div>

                                <form action="{{ route('staff.tables.close', $table->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center justify-center gap-1.5 bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-xs transition">
                                        <i class="fa-solid fa-lock" aria-hidden="true"></i>
                                        <span>Close Table</span>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('staff.tables.open', $table->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-xs transition">
                                        <i class="fa-solid fa-door-open" aria-hidden="true"></i>
                                        <span>Open Table</span>
                                    </button>
                                </form>

                                @if ($canRemoveTables)
                                    <button type="button"
                                            onclick="openRemoveTableModal(this)"
                                            data-action="{{ route('staff.tables.destroy', $table->id) }}"
                                            data-number="{{ $table->table_number }}"
                                            class="w-full flex items-center justify-center gap-1.5 text-xs font-semibold text-stone-400 hover:text-red-600 py-1.5 transition">
                                        <i class="fa-solid fa-trash text-[11px]" aria-hidden="true"></i>
                                        <span>Remove table</span>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    @endif
</div>

@if ($canAddTables)
    {{-- Add Tables Modal --}}
    <div id="addTableModal" class="fixed inset-0 bg-stone-950/60 backdrop-blur-[1px] z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeAddTableModal()">
        <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl">
            <div class="flex justify-between items-center px-5 py-4 border-b border-stone-100">
                <h2 class="text-lg font-extrabold text-stone-900">Add Tables</h2>
                <button type="button" onclick="closeAddTableModal()" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-500 flex items-center justify-center text-lg font-bold">&times;</button>
            </div>

            <form action="{{ route('staff.tables.store') }}" method="POST" class="p-5 flex flex-col gap-3.5">
                @csrf

                <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">How many tables to add?
                    <input type="number" id="tableCount" name="count" min="1" max="100" value="1" required
                           oninput="updateTableRange()"
                           class="mt-1.5 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-bold text-stone-900 focus:outline-none">
                </label>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="setTablePreset(1)" class="flex-1 py-1.5 rounded-lg bg-stone-100 hover:bg-stone-200 text-xs font-bold text-stone-700 transition">+1</button>
                    <button type="button" onclick="setTablePreset(5)" class="flex-1 py-1.5 rounded-lg bg-stone-100 hover:bg-stone-200 text-xs font-bold text-stone-700 transition">+5</button>
                    <button type="button" onclick="setTablePreset(10)" class="flex-1 py-1.5 rounded-lg bg-stone-100 hover:bg-stone-200 text-xs font-bold text-stone-700 transition">+10</button>
                </div>

                <div class="bg-stone-50 border border-stone-100 rounded-xl px-3.5 py-2.5 text-xs text-stone-500 flex items-center justify-between">
                    <span>New table number(s):</span>
                    <span id="tableRange" class="font-extrabold text-stone-900 text-sm">{{ $nextTableNumber }}</span>
                </div>

                <div class="flex gap-2 pt-1">
                    <button type="button" onclick="closeAddTableModal()" class="flex-1 border border-stone-200 hover:bg-stone-50 text-stone-700 font-bold px-4 py-2.5 rounded-xl text-xs transition">Cancel</button>
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition">Add Tables</button>
                </div>
            </form>
        </div>
    </div>
@endif

@if ($canRemoveTables)
    {{-- Remove Table Modal --}}
    <div id="removeTableModal" class="fixed inset-0 bg-stone-950/60 backdrop-blur-[1px] z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeRemoveTableModal()">
        <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl">
            <div class="p-5 text-center">
                <div class="mx-auto mb-3 w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                </div>
                <h2 class="text-lg font-extrabold text-stone-900">Remove table?</h2>
                <p class="text-xs sm:text-sm text-stone-500 mt-1.5">
                    Are you sure you want to remove <strong class="text-stone-800">Table <span id="removeTableNumber"></span></strong>?
                </p>
                <p class="text-xs text-stone-400 mt-2">A table that already has past orders cannot be removed.</p>
            </div>

            <form id="removeTableForm" action="" method="POST" class="flex gap-2 px-5 pb-5">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeRemoveTableModal()" class="flex-1 border border-stone-200 hover:bg-stone-50 text-stone-700 font-bold px-4 py-2.5 rounded-xl text-xs transition">Cancel</button>
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition">Yes, remove</button>
            </form>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    var activeTableFilter = 'all';
    var nextTableNumber = {{ $nextTableNumber }};

    function setTableFilter(status, btn) {
        activeTableFilter = status;
        document.querySelectorAll('.table-filter-btn').forEach(function (el) {
            el.className = 'table-filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-stone-600 hover:text-stone-900 transition';
        });
        btn.className = 'table-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-stone-900 shadow-xs transition';
        filterTables();
    }

    function filterTables() {
        var input = document.getElementById('tableSearchInput');
        if (!input) { return; }

        var query = input.value.trim().toLowerCase();
        var visibleCount = 0;

        document.querySelectorAll('.table-card').forEach(function (card) {
            var matchesQuery = query === '' || card.dataset.number.includes(query);
            var matchesStatus = activeTableFilter === 'all' || card.dataset.status === activeTableFilter;

            if (matchesQuery && matchesStatus) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        var empty = document.getElementById('emptyTableFilter');
        if (empty) {
            empty.classList.toggle('hidden', visibleCount > 0);
        }
    }

    function copyOrderLink(url, btn) {
        var originalHtml = btn.innerHTML;
        var markCopied = function () {
            btn.innerHTML = '<i class="fa-solid fa-check text-emerald-600"></i><span class="text-emerald-700">Copied!</span>';
            setTimeout(function () {
                btn.innerHTML = originalHtml;
            }, 1800);
        };

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(markCopied);
        } else {
            var temp = document.createElement('input');
            temp.value = url;
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);
            markCopied();
        }
    }

    function openAddTableModal() {
        var modal = document.getElementById('addTableModal');
        if (!modal) { return; }
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeAddTableModal() {
        var modal = document.getElementById('addTableModal');
        if (!modal) { return; }
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function setTablePreset(val) {
        var input = document.getElementById('tableCount');
        if (!input) { return; }
        input.value = val;
        updateTableRange();
    }

    function updateTableRange() {
        var count = parseInt(document.getElementById('tableCount').value, 10) || 1;
        var last = nextTableNumber + count - 1;
        document.getElementById('tableRange').innerText = count > 1 ? (nextTableNumber + ' to ' + last) : nextTableNumber;
    }

    function openRemoveTableModal(button) {
        var modal = document.getElementById('removeTableModal');
        if (!modal) { return; }
        document.getElementById('removeTableForm').action = button.dataset.action;
        document.getElementById('removeTableNumber').innerText = button.dataset.number;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeRemoveTableModal() {
        var modal = document.getElementById('removeTableModal');
        if (!modal) { return; }
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAddTableModal();
            closeRemoveTableModal();
        }
    });
</script>
@endpush
