<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff - Food Items</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="max-w-7xl mx-auto p-4 md:p-6">

        <div class="flex justify-between items-center mb-4 gap-3">
            <h1 class="text-2xl font-bold text-gray-800">Manage Food Items</h1>
            <button type="button" onclick="openAddModal()" class="bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm whitespace-nowrap">+ Add New Food Item</button>
        </div>

        @if (session('success'))
            <p class="bg-green-100 text-green-800 p-3 rounded-xl mb-4">{{ session('success') }}</p>
        @endif

        <div class="flex flex-col md:flex-row gap-6">

            {{-- sidebar --}}
            <aside class="md:w-56 flex-shrink-0 md:sticky md:top-6 self-start w-full">
                {{-- cat modal --}}
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Categories</p>
                        <button type="button" onclick="openAddCategoryModal()" class="text-xs font-semibold text-green-700 whitespace-nowrap">+ Add category</button>
                    </div>
                <nav class="flex md:flex-col gap-2 overflow-x-auto md:overflow-visible pb-2 md:pb-0">

                    <a href="{{ route('staff.foodItems.index') }}"
                    class="flex justify-between items-center gap-3 whitespace-nowrap px-4 py-2 rounded-lg text-sm font-semibold {{ $selectedCategory ? 'bg-white text-gray-700 shadow-sm' : 'bg-orange-500 text-white' }}">
                        <span>All</span>
                        <span class="text-xs">{{ $categories->sum('food_items_count') }}</span>
                    </a>

                    @foreach ($categories as $cat)
                        <a href="{{ route('staff.foodItems.index', ['category' => $cat->id]) }}"
                        class="flex justify-between items-center gap-3 whitespace-nowrap px-4 py-2 rounded-lg text-sm font-semibold {{ (int) $selectedCategory === $cat->id ? 'bg-orange-500 text-white' : 'bg-white text-gray-700 shadow-sm' }}">
                            <span>{{ $cat->name }}</span>
                            <span class="text-xs">{{ $cat->food_items_count }}</span>
                        </a>
                    @endforeach

                </nav>
            </aside>

            {{-- items --}}
            <div class="flex-1 min-w-0">

            @php
                $currentCategory = $selectedCategory ? $categories->firstWhere('id', (int) $selectedCategory) : null;
            @endphp

            @if ($currentCategory)
                <div class="bg-white rounded-xl shadow-md p-4 mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-bold text-gray-800 break-words">{{ $currentCategory->name }}</p>
                        <p class="text-sm text-gray-500">{{ $currentCategory->description ?: 'No description' }}</p>
                    </div>

                    <div class="flex gap-2">
                        <button type="button"
                                onclick="openEditCategoryModal(this)"
                                data-action="{{ route('staff.categories.update', $currentCategory->id) }}"
                                data-name="{{ $currentCategory->name }}"
                                data-description="{{ $currentCategory->description }}"
                                class="border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm whitespace-nowrap">Edit category</button>

                        <form action="{{ route('staff.categories.destroy', $currentCategory->id) }}" method="POST"
                            onsubmit="return confirm('Deleting this category will also delete its {{ $currentCategory->food_items_count }} food item(s). Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white font-semibold px-4 py-2 rounded-lg text-sm whitespace-nowrap">Delete category</button>
                        </form>
                    </div>
                </div>
            @endif

                @if ($foodItems->isEmpty())
                    <p class="bg-white rounded-xl shadow-md p-6 text-center text-gray-500">No food items in this category yet.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach ($foodItems as $item)
                            <div class="bg-white rounded-xl shadow-md p-4 flex flex-col">

                                <div class="flex gap-3 mb-3">
                                    @if ($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" class="w-20 h-20 flex-shrink-0 object-cover rounded-lg">
                                    @else
                                        <div class="w-20 h-20 flex-shrink-0 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">No image</div>
                                    @endif

                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-gray-800 leading-snug break-words">{{ $item->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $item->category->name }}</p>
                                        <p class="text-red-800 font-extrabold">RM {{ number_format($item->price, 2) }}</p>
                                    </div>
                                </div>

                                <span class="self-start text-xs font-semibold px-3 py-1 rounded-full mb-3 {{ $item->status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ str_replace('_', ' ', $item->status) }}
                                </span>

                                <div class="mt-auto flex flex-col gap-2">
                                    <form action="{{ route('staff.foodItems.toggleStatus', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="w-full bg-orange-500 text-white font-semibold px-4 py-2 rounded-lg text-sm">
                                            Mark as {{ $item->status === 'available' ? 'Out of Stock' : 'Available' }}
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
                                            class="flex-1 text-center border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm">Edit</button>

                                        <form action="{{ route('staff.foodItems.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');" class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full bg-red-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">Delete</button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        {{-- add food item modal --}}
            <div id="addModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeAddModal()">
                <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">

                    <div class="flex justify-between items-center p-4 border-b">
                        <h2 class="text-lg font-bold text-gray-800">Add New Food Item</h2>
                        <button type="button" onclick="closeAddModal()" class="text-2xl text-gray-400">&times;</button>
                    </div>

                    <form action="{{ route('staff.foodItems.store') }}" method="POST" enctype="multipart/form-data" class="p-4 flex flex-col gap-3">
                        @csrf

                        <label class="text-sm font-semibold text-gray-700">Category
                            <select name="category_id" class="mt-1 w-full p-2 border border-gray-200 rounded-lg bg-white font-normal">
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (int) $selectedCategory === $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="text-sm font-semibold text-gray-700">Name
                            <input type="text" name="name" required class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal">
                        </label>

                        <label class="text-sm font-semibold text-gray-700">Description
                            <textarea name="description" rows="2" class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal"></textarea>
                        </label>

                        <div class="flex gap-3">
                            <label class="flex-1 text-sm font-semibold text-gray-700">Price (RM)
                                <input type="number" step="0.01" min="0" name="price" required class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal">
                            </label>
                            <label class="flex-1 text-sm font-semibold text-gray-700">Unit cost (RM)
                                <input type="number" step="0.01" min="0" name="unit_cost" class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal">
                            </label>
                        </div>

                        <label class="text-sm font-semibold text-gray-700">Image
                            <input type="file" name="image" accept="image/*" class="mt-1 w-full text-sm font-normal">
                        </label>

                        <div class="flex gap-2 pt-2">
                            <button type="button" onclick="closeAddModal()" class="flex-1 border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm">Cancel</button>
                            <button type="submit" class="flex-1 bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">Add Item</button>
                        </div>
                    </form>
                </div>
            </div>

    {{-- edit food item modal --}}
    <div id="editModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeEditModal()">
        <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">

            <div class="flex justify-between items-center p-4 border-b">
                <h2 class="text-lg font-bold text-gray-800">Edit Food Item</h2>
                <button type="button" onclick="closeEditModal()" class="text-2xl text-gray-400">&times;</button>
            </div>

            <form id="editForm" action="" method="POST" enctype="multipart/form-data" class="p-4 flex flex-col gap-3">
                @csrf
                @method('PUT')

                <label class="text-sm font-semibold text-gray-700">Category
                    <select name="category_id" class="mt-1 w-full p-2 border border-gray-200 rounded-lg bg-white font-normal">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="text-sm font-semibold text-gray-700">Name
                    <input type="text" name="name" required class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal">
                </label>

                <label class="text-sm font-semibold text-gray-700">Description
                    <textarea name="description" rows="2" class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal"></textarea>
                </label>

                <div class="flex gap-3">
                    <label class="flex-1 text-sm font-semibold text-gray-700">Price (RM)
                        <input type="number" step="0.01" min="0" name="price" required class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal">
                    </label>
                    <label class="flex-1 text-sm font-semibold text-gray-700">Unit cost (RM)
                        <input type="number" step="0.01" min="0" name="unit_cost" class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal">
                    </label>
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-700 mb-1">Image</p>
                    <img id="editImagePreview" src="" class="hidden w-24 h-24 object-cover rounded-lg mb-2">
                    <input type="file" name="image" accept="image/*" class="w-full text-sm">
                    <p class="text-xs text-gray-400 mt-1">Leave empty to keep the current image.</p>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="closeEditModal()" class="flex-1 border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm">Cancel</button>
                    <button type="submit" class="flex-1 bg-orange-500 text-white font-semibold px-4 py-2 rounded-lg text-sm">Save Changes</button>
                </div>
            </form>

        </div>
    </div>

        {{-- add category modal --}}
    <div id="addCategoryModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeAddCategoryModal()">
        <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">

            <div class="flex justify-between items-center p-4 border-b">
                <h2 class="text-lg font-bold text-gray-800">Add New Category</h2>
                <button type="button" onclick="closeAddCategoryModal()" class="text-2xl text-gray-400">&times;</button>
            </div>

            <form action="{{ route('staff.categories.store') }}" method="POST" class="p-4 flex flex-col gap-3">
                @csrf

                <label class="text-sm font-semibold text-gray-700">Name
                    <input type="text" name="name" required class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal">
                </label>

                <label class="text-sm font-semibold text-gray-700">Description
                    <textarea name="description" rows="2" class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal"></textarea>
                </label>

                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="closeAddCategoryModal()" class="flex-1 border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm">Cancel</button>
                    <button type="submit" class="flex-1 bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">Add Category</button>
                </div>
            </form>

        </div>
    </div>

    {{-- edit category modal --}}
    <div id="editCategoryModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeEditCategoryModal()">
        <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">

            <div class="flex justify-between items-center p-4 border-b">
                <h2 class="text-lg font-bold text-gray-800">Edit Category</h2>
                <button type="button" onclick="closeEditCategoryModal()" class="text-2xl text-gray-400">&times;</button>
            </div>

            <form id="editCategoryForm" action="" method="POST" class="p-4 flex flex-col gap-3">
                @csrf
                @method('PUT')

                <label class="text-sm font-semibold text-gray-700">Name
                    <input type="text" name="name" required class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal">
                </label>

                <label class="text-sm font-semibold text-gray-700">Description
                    <textarea name="description" rows="2" class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal"></textarea>
                </label>

                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="closeEditCategoryModal()" class="flex-1 border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm">Cancel</button>
                    <button type="submit" class="flex-1 bg-orange-500 text-white font-semibold px-4 py-2 rounded-lg text-sm">Save Changes</button>
                </div>
            </form>

        </div>
    </div>

        </div>
    </div>

    <script>
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
        document.getElementById('addModal').classList.add('flex');
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
        document.getElementById('addModal').classList.remove('flex');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeAddModal();
    });function openAddModal() {
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


    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAddModal();
            closeEditModal();
        }
    });



    </script>
</body>
</html>
