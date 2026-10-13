{{-- <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Categories</title>
</head>
<body>
    <h1>Manage Categories</h1>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <a href="{{ route('staff.categories.create') }}">Add New Category</a>

    @foreach ($categories as $category)
        <div style="border: 1px solid #ccc; margin-bottom: 10px; padding: 10px;">
            <strong>{{ $category->name }}</strong>
            <p>{{ $category->description }}</p>

            <a href="{{ route('staff.categories.edit', $category->id) }}">Edit</a>

            <form action="{{ route('staff.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Deleting this category will also delete ALL food items inside it. Are you sure?');" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit">Delete</button>
            </form>
        </div>
    @endforeach
</body>
</html> --}}

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="max-w-5xl mx-auto p-4 md:p-6">

        <div class="flex justify-between items-center mb-4 gap-3">
            <h1 class="text-2xl font-bold text-gray-800">Manage Categories</h1>
            <button type="button" onclick="openAddModal()" class="bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm whitespace-nowrap">+ Add New Category</button>
        </div>

        @if (session('success'))
            <p class="bg-green-100 text-green-800 p-3 rounded-xl mb-4">{{ session('success') }}</p>
        @endif

        @if ($categories->isEmpty())
            <p class="bg-white rounded-xl shadow-md p-6 text-center text-gray-500">No categories yet. Add your first one.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach ($categories as $category)
                    <div class="bg-white rounded-xl shadow-md p-4 flex flex-col">

                        <div class="flex justify-between items-start gap-3 mb-1">
                            <p class="font-bold text-gray-800 break-words">{{ $category->name }}</p>
                            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-gray-100 text-gray-600 whitespace-nowrap">
                                {{ $category->food_items_count }} {{ $category->food_items_count == 1 ? 'item' : 'items' }}
                            </span>
                        </div>

                        <p class="text-sm text-gray-500 mb-4">{{ $category->description ?: 'No description' }}</p>

                        <div class="mt-auto flex gap-2">
                            <button type="button"
                                    onclick="openEditModal(this)"
                                    data-action="{{ route('staff.categories.update', $category->id) }}"
                                    data-name="{{ $category->name }}"
                                    data-description="{{ $category->description }}"
                                    class="flex-1 text-center border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm">Edit</button>

                            <form action="{{ route('staff.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Deleting this category will also delete its {{ $category->food_items_count }} food item(s). Are you sure?');" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">Delete</button>
                            </form>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- add category modal --}}
    <div id="addModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeAddModal()">
        <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">

            <div class="flex justify-between items-center p-4 border-b">
                <h2 class="text-lg font-bold text-gray-800">Add New Category</h2>
                <button type="button" onclick="closeAddModal()" class="text-2xl text-gray-400">&times;</button>
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
                    <button type="button" onclick="closeAddModal()" class="flex-1 border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm">Cancel</button>
                    <button type="submit" class="flex-1 bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">Add Category</button>
                </div>
            </form>

        </div>
    </div>

    {{-- edit category modal --}}
    <div id="editModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeEditModal()">
        <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">

            <div class="flex justify-between items-center p-4 border-b">
                <h2 class="text-lg font-bold text-gray-800">Edit Category</h2>
                <button type="button" onclick="closeEditModal()" class="text-2xl text-gray-400">&times;</button>
            </div>

            <form id="editForm" action="" method="POST" class="p-4 flex flex-col gap-3">
                @csrf
                @method('PUT')

                <label class="text-sm font-semibold text-gray-700">Name
                    <input type="text" name="name" required class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal">
                </label>

                <label class="text-sm font-semibold text-gray-700">Description
                    <textarea name="description" rows="2" class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal"></textarea>
                </label>

                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="closeEditModal()" class="flex-1 border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm">Cancel</button>
                    <button type="submit" class="flex-1 bg-orange-500 text-white font-semibold px-4 py-2 rounded-lg text-sm">Save Changes</button>
                </div>
            </form>

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

    function openEditModal(button) {
        var form = document.getElementById('editForm');

        form.action = button.dataset.action;
        form.querySelector('[name="name"]').value = button.dataset.name;
        form.querySelector('[name="description"]').value = button.dataset.description;

        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editModal').classList.add('flex');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('editModal').classList.remove('flex');
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
