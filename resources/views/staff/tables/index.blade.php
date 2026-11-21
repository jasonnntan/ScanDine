@extends('layouts.staff')

@section('title', 'Tables')

@section('content')
<div class="max-w-5xl mx-auto p-4 md:p-6">

    @php
        $canAddTables = true;     // staff, manager and owner can all add tables
        $canRemoveTables = true;  // and remove a table that has never had an order
        $nextTableNumber = (int) $tables->map(function ($t) { return (int) $t->table_number; })->max() + 1;
    @endphp

    <div class="flex justify-between items-center mb-4 gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Restaurant Tables</h1>
            <p class="text-sm text-gray-500">{{ $tables->count() }} table(s), {{ $tables->where('is_occupied', true)->count() }} occupied</p>
        </div>

        @if ($canAddTables)
            <button type="button" onclick="openAddTableModal()" class="bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm whitespace-nowrap">+ Add Tables</button>
        @endif
    </div>

    @if (session('error'))
        <p class="bg-red-100 text-red-800 p-3 rounded-xl mb-4">{{ session('error') }}</p>
    @endif

    @if (session('success'))
        <p class="bg-green-100 text-green-800 p-3 rounded-xl mb-4 break-all">{{ session('success') }}</p>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach ($tables as $table)
            <div class="bg-white rounded-xl shadow-md p-4 text-center border-2 {{ $table->is_occupied ? 'border-red-500' : 'border-green-500' }}">

                <h2 class="text-xl font-bold text-gray-800">Table {{ $table->table_number }}</h2>
                <p class="font-semibold mb-3 {{ $table->is_occupied ? 'text-red-600' : 'text-green-600' }}">
                    {{ $table->is_occupied ? 'Occupied' : 'Free' }}
                </p>

                @if ($table->is_occupied)
                    <div class="flex justify-center mb-2">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->generate(url('/order/' . $table->qr_token)) !!}
                    </div>
                    <p class="text-xs text-gray-400 break-all mb-3">{{ url('/order/' . $table->qr_token) }}</p>

                    <a href="{{ route('staff.tables.print', $table->id) }}" target="_blank" class="block w-full border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm mb-2">🖨️ Print QR Slip</a>

                    <form action="{{ route('staff.tables.close', $table->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-red-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">Close Table</button>
                    </form>
                @else
                    <form action="{{ route('staff.tables.open', $table->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">Open Table</button>
                    </form>

                    @if ($canRemoveTables)
                        <button type="button"
                                onclick="openRemoveTableModal(this)"
                                data-action="{{ route('staff.tables.destroy', $table->id) }}"
                                data-number="{{ $table->table_number }}"
                                class="w-full mt-2 text-sm text-gray-400 hover:text-red-600 py-1">Remove table</button>
                    @endif
                @endif

            </div>
        @endforeach
    </div>
</div>

@if ($canAddTables)
    {{-- add tables modal --}}
    <div id="addTableModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeAddTableModal()">
        <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden">

            <div class="flex justify-between items-center p-4 border-b">
                <h2 class="text-lg font-bold text-gray-800">Add Tables</h2>
                <button type="button" onclick="closeAddTableModal()" class="text-2xl text-gray-400">&times;</button>
            </div>

            <form action="{{ route('staff.tables.store') }}" method="POST" class="p-4 flex flex-col gap-3">
                @csrf

                <label class="text-sm font-semibold text-gray-700">How many tables to add?
                    <input type="number" id="tableCount" name="count" min="1" max="100" value="1" required
                           oninput="updateTableRange()"
                           class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal">
                </label>

                <p class="text-sm text-gray-500">New table number(s): <span id="tableRange" class="font-semibold text-gray-800">{{ $nextTableNumber }}</span></p>

                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="closeAddTableModal()" class="flex-1 border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm">Cancel</button>
                    <button type="submit" class="flex-1 bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">Add Tables</button>
                </div>
            </form>

        </div>
    </div>
@endif

@if ($canRemoveTables)
    {{-- remove table modal --}}
    <div id="removeTableModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeRemoveTableModal()">
        <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden">

            <div class="p-5 text-center">
                <div class="mx-auto mb-3 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-2xl">🗑️</div>
                <h2 class="text-lg font-bold text-gray-800">Remove table?</h2>
                <p class="text-sm text-gray-500 mt-2">
                    Are you sure you want to remove Table
                    <span id="removeTableNumber" class="font-semibold text-gray-800"></span>?
                </p>
                <p class="text-sm text-gray-400 mt-2">A table that already has orders cannot be removed.</p>
            </div>

            <form id="removeTableForm" action="" method="POST" class="flex gap-2 p-4 pt-0">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeRemoveTableModal()" class="flex-1 border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm">Cancel</button>
                <button type="submit" class="flex-1 bg-red-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">Yes, remove</button>
            </form>

        </div>
    </div>
@endif
@endsection

@push('scripts')
@if ($canAddTables)
<script>
    var nextTableNumber = {{ $nextTableNumber }};

    function openAddTableModal() {
        document.getElementById('addTableModal').classList.remove('hidden');
        document.getElementById('addTableModal').classList.add('flex');
    }

    function closeAddTableModal() {
        document.getElementById('addTableModal').classList.add('hidden');
        document.getElementById('addTableModal').classList.remove('flex');
    }

    function updateTableRange() {
        var count = parseInt(document.getElementById('tableCount').value) || 1;
        var last = nextTableNumber + count - 1;

        document.getElementById('tableRange').innerText = count > 1 ? (nextTableNumber + ' to ' + last) : nextTableNumber;
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAddTableModal();
        }
    });
</script>
@endif

@if ($canRemoveTables)
<script>
    function openRemoveTableModal(button) {
        document.getElementById('removeTableForm').action = button.dataset.action;
        document.getElementById('removeTableNumber').innerText = button.dataset.number;

        document.getElementById('removeTableModal').classList.remove('hidden');
        document.getElementById('removeTableModal').classList.add('flex');
    }

    function closeRemoveTableModal() {
        document.getElementById('removeTableModal').classList.add('hidden');
        document.getElementById('removeTableModal').classList.remove('flex');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeRemoveTableModal();
        }
    });
</script>
@endif
@endpush
