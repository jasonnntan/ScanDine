@extends('layouts.staff')

@section('title', 'Food Items')

@section('content')
<div class="max-w-7xl mx-auto p-4 md:p-6 space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Menu & Food Items</h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                Manage dishes, pricing, and live stock availability
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <button type="button" onclick="openAddCategoryModal()" class="inline-flex items-center gap-1.5 bg-white border border-stone-200 hover:bg-stone-50 text-stone-700 font-bold px-3.5 py-2.5 rounded-xl text-xs shadow-sm transition">
                <i class="fa-solid fa-folder-plus text-stone-400" aria-hidden="true"></i>
                <span>New Category</span>
            </button>
            <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-sm transition">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                <span>Add Food Item</span>
            </button>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">

        {{-- Categories Sidebar --}}
        <aside class="lg:w-60 flex-shrink-0 lg:sticky lg:top-6 self-start w-full">
            <div class="flex justify-between items-center mb-2.5 px-1">
                <p class="text-[11px] font-extrabold text-stone-400 uppercase tracking-wider">Categories</p>
                <button type="button" onclick="openAddCategoryModal()" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 transition">
                    + Add
                </button>
            </div>

            <nav class="flex lg:flex-col gap-2 overflow-x-auto lg:overflow-visible pb-2 lg:pb-0">
                <a href="{{ route('staff.foodItems.index') }}"
                   class="flex-shrink-0 flex justify-between items-center gap-3 whitespace-nowrap px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition {{ $selectedCategory ? 'bg-white border border-stone-200/80 text-stone-700 hover:bg-stone-50' : 'bg-orange-600 text-white shadow-sm shadow-orange-600/20' }}">
                    <span>All Menu Items</span>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $selectedCategory ? 'bg-stone-100 text-stone-600' : 'bg-white/20 text-white' }}">
                        {{ $categories->sum('food_items_count') }}
                    </span>
                </a>

                @foreach ($categories as $cat)
                    @php $isActive = (int) $selectedCategory === $cat->id; @endphp

                    <div class="flex-shrink-0 flex items-center rounded-xl text-xs sm:text-sm font-bold transition {{ $isActive ? 'bg-orange-600 text-white shadow-sm shadow-orange-600/20' : 'bg-white border border-stone-200/80 text-stone-700 hover:bg-stone-50' }}">
                        <a href="{{ route('staff.foodItems.index', ['category' => $cat->id]) }}"
                           class="flex-1 min-w-0 flex justify-between items-center gap-2 whitespace-nowrap pl-3.5 pr-2.5 py-2.5">
                            <span class="truncate">{{ $cat->name }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $isActive ? 'bg-white/20 text-white' : 'bg-stone-100 text-stone-500' }}">
                                {{ $cat->food_items_count }}
                            </span>
                        </a>

                        @if ($isActive)
                            <div class="flex items-center pr-1.5 gap-0.5">
                                <button type="button"
                                        onclick="openEditCategoryModal(this)"
                                        data-action="{{ route('staff.categories.update', $cat->id) }}"
                                        data-name="{{ $cat->name }}"
                                        data-description="{{ $cat->description }}"
                                        class="w-7 h-7 rounded-lg hover:bg-orange-700 flex items-center justify-center text-xs transition"
                                        title="Edit category" aria-label="Edit category">
                                    <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                </button>

                                <button type="button"
                                        onclick="openDeleteCategoryModal(this)"
                                        data-action="{{ route('staff.categories.destroy', $cat->id) }}"
                                        data-name="{{ $cat->name }}"
                                        data-count="{{ $cat->food_items_count }}"
                                        class="w-7 h-7 rounded-lg hover:bg-orange-700 flex items-center justify-center text-xs transition"
                                        title="Delete category" aria-label="Delete category">
                                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </nav>
        </aside>

        {{-- Main Food Grid Area --}}
        <div class="flex-1 min-w-0 space-y-4">

            {{-- Search & Stock Status Toolbar --}}
            <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-3.5 flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass text-stone-400 text-xs absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" aria-hidden="true"></i>
                    <input type="text" id="staffFoodSearch" oninput="filterStaffFood()" placeholder="Search dish name or description..."
                           class="w-full pl-9 pr-3 py-2 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm text-stone-800 focus:outline-none">
                </div>

                <div class="flex items-center gap-1.5 bg-stone-100 p-1 rounded-xl self-start sm:self-auto">
                    <button type="button" onclick="setStockFilter('all', this)" class="stock-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-stone-900 shadow-xs transition">
                        All
                    </button>
                    <button type="button" onclick="setStockFilter('available', this)" class="stock-filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-stone-600 hover:text-stone-900 transition">
                        Available
                    </button>
                    <button type="button" onclick="setStockFilter('out_of_stock', this)" class="stock-filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-stone-600 hover:text-stone-900 transition">
                        Out of Stock
                    </button>
                </div>
            </div>

            @if ($foodItems->isEmpty())
                <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-12 text-center">
                    <div class="w-12 h-12 rounded-full bg-stone-100 text-stone-400 flex items-center justify-center mx-auto mb-3 text-lg">
                        <i class="fa-solid fa-utensils" aria-hidden="true"></i>
                    </div>
                    <p class="font-bold text-stone-800">No food items in this category yet</p>
                    <p class="text-xs text-stone-400 mt-1">Click "Add Food Item" above to create your first dish.</p>
                </div>
            @else
                <div id="emptyFoodFilter" class="hidden bg-white rounded-2xl border border-stone-200/80 shadow-sm p-10 text-center">
                    <p class="font-bold text-stone-700">No matching items found</p>
                    <p class="text-xs text-stone-400 mt-1">Try clearing your search or switching the stock filter.</p>
                </div>

                <div id="staffFoodGrid" class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-4">
                    @foreach ($foodItems as $item)
                        @php
                            $isAvailable = $item->status === 'available';
                            $hasCost = $item->unit_cost !== null && $item->unit_cost > 0;
                            $margin = $hasCost ? ($item->price - $item->unit_cost) : null;
                        @endphp

                        <div class="staff-food-card bg-white rounded-2xl border border-stone-200/80 shadow-sm p-4 flex flex-col justify-between transition hover:shadow-md {{ !$isAvailable ? 'bg-stone-50/70' : '' }}"
                             data-name="{{ strtolower($item->name) }}"
                             data-desc="{{ strtolower($item->description ?? '') }}"
                             data-status="{{ $isAvailable ? 'available' : 'out_of_stock' }}">

                            <div>
                                <div class="flex gap-3.5 mb-3">
                                    @if ($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-20 h-20 flex-shrink-0 object-cover rounded-xl {{ !$isAvailable ? 'grayscale opacity-75' : '' }}">
                                    @else
                                        <div class="w-20 h-20 flex-shrink-0 rounded-xl bg-stone-100 flex flex-col items-center justify-center text-stone-400 text-[11px]">
                                            <i class="fa-regular fa-image text-base mb-1" aria-hidden="true"></i>
                                            <span>No photo</span>
                                        </div>
                                    @endif

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-2">
                                            <p class="font-extrabold text-stone-900 text-sm sm:text-base leading-snug line-clamp-2">{{ $item->name }}</p>
                                        </div>
                                        <p class="text-xs font-medium text-stone-400 mt-0.5">{{ $item->category->name }}</p>

                                        <div class="flex flex-wrap items-baseline gap-2 mt-1.5">
                                            <span class="text-red-800 font-extrabold text-sm tabular-nums">RM {{ number_format($item->price, 2) }}</span>
                                            @if ($hasCost)
                                                <span class="text-[11px] font-semibold text-stone-400 tabular-nums">
                                                    Cost RM {{ number_format($item->unit_cost, 2) }} · +RM {{ number_format($margin, 2) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if ($item->description)
                                    <p class="text-xs text-stone-500 line-clamp-2 mb-3">{{ $item->description }}</p>
                                @endif

                                <div class="mb-3">
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full {{ $isAvailable ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/80' : 'bg-red-50 text-red-700 border border-red-200/80' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $isAvailable ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                        {{ str_replace('_', ' ', $item->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-stone-100 flex flex-col gap-2">
                                <form action="{{ route('staff.foodItems.toggleStatus', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="w-full font-bold px-3.5 py-2 rounded-xl text-xs transition {{ $isAvailable ? 'bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs' }}">
                                        {{ $isAvailable ? 'Mark as Out of Stock' : 'Restock (Mark Available)' }}
                                    </button>
                                </form>

                                <div class="flex gap-2">
                                    <button type="button"
                                            onclick="openEditModal(this)"
                                            data-action="{{ route('staff.foodItems.update', $item->id) }}"
                                            data-category="{{ $item->category_id }}"
                                            data-name="{{ $item->name }}"
                                            data-description="{{ $item->description }}"
                                            data-price="{{ $item->price }}"
                                            data-unit-cost="{{ $item->unit_cost }}"
                                            data-image="{{ $item->image ? asset('storage/' . $item->image) : '' }}"
                                            class="flex-1 flex items-center justify-center gap-1.5 border border-stone-200 hover:bg-stone-50 text-stone-700 font-bold px-3 py-2 rounded-xl text-xs transition">
                                        <i class="fa-solid fa-pen text-[11px] text-stone-400" aria-hidden="true"></i>
                                        <span>Edit</span>
                                    </button>

                                    <button type="button"
                                            onclick="openDeleteItemModal(this)"
                                            data-action="{{ route('staff.foodItems.destroy', $item->id) }}"
                                            data-name="{{ $item->name }}"
                                            class="flex-1 flex items-center justify-center gap-1.5 border border-red-200 bg-red-50/60 hover:bg-red-100 text-red-700 font-bold px-3 py-2 rounded-xl text-xs transition">
                                        <i class="fa-solid fa-trash text-[11px]" aria-hidden="true"></i>
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>

{{-- Add Food Item Modal --}}
<div id="addModal" class="fixed inset-0 bg-stone-950/60 backdrop-blur-[1px] z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeAddModal()">
    <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="flex justify-between items-center px-5 py-4 border-b border-stone-100">
            <h2 class="text-lg font-extrabold text-stone-900">Add New Food Item</h2>
            <button type="button" onclick="closeAddModal()" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-500 flex items-center justify-center text-lg font-bold">&times;</button>
        </div>

        <form action="{{ route('staff.foodItems.store') }}" method="POST" enctype="multipart/form-data" class="p-5 flex flex-col gap-3.5">
            @csrf

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Category
                <select name="category_id" class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-medium focus:outline-none">
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (int) $selectedCategory === $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Item Name
                <input type="text" name="name" required placeholder="e.g. Nasi Lemak Ayam Goreng" class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-normal focus:outline-none">
            </label>

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Description
                <textarea name="description" rows="2" placeholder="Ingredients, spiciness, or portion info" class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-normal focus:outline-none"></textarea>
            </label>

            <div class="grid grid-cols-2 gap-3">
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Selling Price (RM)
                    <input type="number" step="0.01" min="0" name="price" required placeholder="0.00" class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-normal focus:outline-none">
                </label>
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Unit Cost (RM)
                    <input type="number" step="0.01" min="0" name="unit_cost" placeholder="Optional" class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-normal focus:outline-none">
                </label>
            </div>

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Dish Photo
                <input type="file" name="image" accept="image/*" class="mt-1 w-full text-xs text-stone-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
            </label>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeAddModal()" class="flex-1 border border-stone-200 hover:bg-stone-50 text-stone-700 font-bold px-4 py-2.5 rounded-xl text-xs transition">Cancel</button>
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition">Add Item</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Food Item Modal --}}
<div id="editModal" class="fixed inset-0 bg-stone-950/60 backdrop-blur-[1px] z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeEditModal()">
    <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="flex justify-between items-center px-5 py-4 border-b border-stone-100">
            <h2 class="text-lg font-extrabold text-stone-900">Edit Food Item</h2>
            <button type="button" onclick="closeEditModal()" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-500 flex items-center justify-center text-lg font-bold">&times;</button>
        </div>

        <form id="editForm" action="" method="POST" enctype="multipart/form-data" class="p-5 flex flex-col gap-3.5">
            @csrf
            @method('PUT')

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Category
                <select name="category_id" class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-medium focus:outline-none">
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Item Name
                <input type="text" name="name" required class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-normal focus:outline-none">
            </label>

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Description
                <textarea name="description" rows="2" class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-normal focus:outline-none"></textarea>
            </label>

            <div class="grid grid-cols-2 gap-3">
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Selling Price (RM)
                    <input type="number" step="0.01" min="0" name="price" required class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-normal focus:outline-none">
                </label>
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Unit Cost (RM)
                    <input type="number" step="0.01" min="0" name="unit_cost" class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-normal focus:outline-none">
                </label>
            </div>

            <div>
                <p class="text-xs font-bold text-stone-600 uppercase tracking-wide mb-1.5">Dish Photo</p>
                <img id="editImagePreview" src="" alt="Preview" class="hidden w-24 h-24 object-cover rounded-xl border border-stone-200 mb-2">
                <input type="file" name="image" accept="image/*" class="w-full text-xs text-stone-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                <p class="text-[11px] text-stone-400 mt-1">Leave empty to keep the current photo.</p>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeEditModal()" class="flex-1 border border-stone-200 hover:bg-stone-50 text-stone-700 font-bold px-4 py-2.5 rounded-xl text-xs transition">Cancel</button>
                <button type="submit" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition">Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Food Item Modal --}}
<div id="deleteItemModal" class="fixed inset-0 bg-stone-950/60 backdrop-blur-[1px] z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeDeleteItemModal()">
    <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl">
        <div class="p-5 text-center">
            <div class="mx-auto mb-3 w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-trash" aria-hidden="true"></i>
            </div>
            <h2 class="text-lg font-extrabold text-stone-900">Delete food item?</h2>
            <p class="text-xs sm:text-sm text-stone-500 mt-1.5">
                <span id="deleteItemName" class="font-bold text-stone-800"></span> will be removed from the customer menu. Past orders and sales records remain intact.
            </p>
        </div>

        <form id="deleteItemForm" action="" method="POST" class="flex gap-2 px-5 pb-5">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteItemModal()" class="flex-1 border border-stone-200 hover:bg-stone-50 text-stone-700 font-bold px-4 py-2.5 rounded-xl text-xs transition">Cancel</button>
            <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition">Yes, delete</button>
        </form>
    </div>
</div>

{{-- Add Category Modal --}}
<div id="addCategoryModal" class="fixed inset-0 bg-stone-950/60 backdrop-blur-[1px] z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeAddCategoryModal()">
    <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="flex justify-between items-center px-5 py-4 border-b border-stone-100">
            <h2 class="text-lg font-extrabold text-stone-900">Add New Category</h2>
            <button type="button" onclick="closeAddCategoryModal()" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-500 flex items-center justify-center text-lg font-bold">&times;</button>
        </div>

        <form action="{{ route('staff.categories.store') }}" method="POST" class="p-5 flex flex-col gap-3.5">
            @csrf

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Category Name
                <input type="text" name="name" required placeholder="e.g. Rice & Noodles" class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-normal focus:outline-none">
            </label>

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Description (optional)
                <textarea name="description" rows="2" class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-normal focus:outline-none"></textarea>
            </label>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeAddCategoryModal()" class="flex-1 border border-stone-200 hover:bg-stone-50 text-stone-700 font-bold px-4 py-2.5 rounded-xl text-xs transition">Cancel</button>
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition">Add Category</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Category Modal --}}
<div id="editCategoryModal" class="fixed inset-0 bg-stone-950/60 backdrop-blur-[1px] z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeEditCategoryModal()">
    <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="flex justify-between items-center px-5 py-4 border-b border-stone-100">
            <h2 class="text-lg font-extrabold text-stone-900">Edit Category</h2>
            <button type="button" onclick="closeEditCategoryModal()" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-500 flex items-center justify-center text-lg font-bold">&times;</button>
        </div>

        <form id="editCategoryForm" action="" method="POST" class="p-5 flex flex-col gap-3.5">
            @csrf
            @method('PUT')

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Category Name
                <input type="text" name="name" required class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-normal focus:outline-none">
            </label>

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Description (optional)
                <textarea name="description" rows="2" class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-normal focus:outline-none"></textarea>
            </label>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeEditCategoryModal()" class="flex-1 border border-stone-200 hover:bg-stone-50 text-stone-700 font-bold px-4 py-2.5 rounded-xl text-xs transition">Cancel</button>
                <button type="submit" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition">Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Category Modal --}}
<div id="deleteCategoryModal" class="fixed inset-0 bg-stone-950/60 backdrop-blur-[1px] z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeDeleteCategoryModal()">
    <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl">
        <div class="p-5 text-center">
            <div class="mx-auto mb-3 w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-trash" aria-hidden="true"></i>
            </div>
            <h2 class="text-lg font-extrabold text-stone-900">Delete category?</h2>
            <p class="text-xs sm:text-sm text-stone-500 mt-1.5">
                Are you sure you want to delete
                <span id="deleteCategoryName" class="font-bold text-stone-800"></span>?
            </p>
            <p id="deleteCategoryWarning" class="text-xs font-semibold text-red-600 mt-2"></p>
        </div>

        <form id="deleteCategoryForm" action="" method="POST" class="flex gap-2 px-5 pb-5">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteCategoryModal()" class="flex-1 border border-stone-200 hover:bg-stone-50 text-stone-700 font-bold px-4 py-2.5 rounded-xl text-xs transition">Cancel</button>
            <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition">Yes, delete</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var activeStockFilter = 'all';

    function setStockFilter(status, btn) {
        activeStockFilter = status;
        document.querySelectorAll('.stock-filter-btn').forEach(function (el) {
            el.className = 'stock-filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-stone-600 hover:text-stone-900 transition';
        });
        btn.className = 'stock-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-stone-900 shadow-xs transition';
        filterStaffFood();
    }

    function filterStaffFood() {
        var searchInput = document.getElementById('staffFoodSearch');
        if (!searchInput) { return; }

        var query = searchInput.value.trim().toLowerCase();
        var cards = document.querySelectorAll('.staff-food-card');
        var visibleCount = 0;

        cards.forEach(function (card) {
            var matchesQuery = query === '' ||
                card.dataset.name.includes(query) ||
                card.dataset.desc.includes(query);
            var matchesStock = activeStockFilter === 'all' || card.dataset.status === activeStockFilter;

            if (matchesQuery && matchesStock) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        var emptyState = document.getElementById('emptyFoodFilter');
        if (emptyState) {
            emptyState.classList.toggle('hidden', visibleCount > 0);
        }
    }

    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
        document.getElementById('addModal').classList.add('flex');
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
        document.getElementById('addModal').classList.remove('flex');
    }

    function openEditModal(button) {
        var form = document.getElementById('editForm');

        form.action = button.dataset.action;
        form.querySelector('[name="category_id"]').value = button.dataset.category;
        form.querySelector('[name="name"]').value = button.dataset.name;
        form.querySelector('[name="description"]').value = button.dataset.description;
        form.querySelector('[name="price"]').value = button.dataset.price;
        form.querySelector('[name="unit_cost"]').value = button.dataset.unitCost;
        form.querySelector('[name="image"]').value = '';

        var preview = document.getElementById('editImagePreview');
        if (button.dataset.image) {
            preview.src = button.dataset.image;
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }

        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editModal').classList.add('flex');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('editModal').classList.remove('flex');
    }

    function openDeleteItemModal(button) {
        document.getElementById('deleteItemForm').action = button.dataset.action;
        document.getElementById('deleteItemName').innerText = button.dataset.name;

        document.getElementById('deleteItemModal').classList.remove('hidden');
        document.getElementById('deleteItemModal').classList.add('flex');
    }

    function closeDeleteItemModal() {
        document.getElementById('deleteItemModal').classList.add('hidden');
        document.getElementById('deleteItemModal').classList.remove('flex');
    }

    function openAddCategoryModal() {
        document.getElementById('addCategoryModal').classList.remove('hidden');
        document.getElementById('addCategoryModal').classList.add('flex');
    }

    function closeAddCategoryModal() {
        document.getElementById('addCategoryModal').classList.add('hidden');
        document.getElementById('addCategoryModal').classList.remove('flex');
    }

    function openEditCategoryModal(button) {
        var form = document.getElementById('editCategoryForm');

        form.action = button.dataset.action;
        form.querySelector('[name="name"]').value = button.dataset.name;
        form.querySelector('[name="description"]').value = button.dataset.description;

        document.getElementById('editCategoryModal').classList.remove('hidden');
        document.getElementById('editCategoryModal').classList.add('flex');
    }

    function closeEditCategoryModal() {
        document.getElementById('editCategoryModal').classList.add('hidden');
        document.getElementById('editCategoryModal').classList.remove('flex');
    }

    function openDeleteCategoryModal(button) {
        document.getElementById('deleteCategoryForm').action = button.dataset.action;
        document.getElementById('deleteCategoryName').innerText = button.dataset.name;

        var count = parseInt(button.dataset.count, 10);
        document.getElementById('deleteCategoryWarning').innerText = count > 0
            ? 'Its ' + count + ' food item(s) will also be removed from the menu. Past orders and sales stay.'
            : 'Past orders and sales stay.';

        document.getElementById('deleteCategoryModal').classList.remove('hidden');
        document.getElementById('deleteCategoryModal').classList.add('flex');
    }

    function closeDeleteCategoryModal() {
        document.getElementById('deleteCategoryModal').classList.add('hidden');
        document.getElementById('deleteCategoryModal').classList.remove('flex');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAddModal();
            closeEditModal();
            closeDeleteItemModal();
            closeAddCategoryModal();
            closeEditCategoryModal();
            closeDeleteCategoryModal();
        }
    });
</script>
@endpush
