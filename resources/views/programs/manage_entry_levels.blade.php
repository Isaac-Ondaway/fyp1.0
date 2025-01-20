<x-app-layout>
    <div class="container mx-auto px-6 py-8">
        <h1 class="text-2xl font-bold mb-6 text-white">Manage Entry Levels for Programs</h1>

        <!-- Filter Section -->
        <form method="GET" action="{{ route('programs.manage_entry_levels') }}" class="flex space-x-4 mb-6">
            <!-- Batch Filter -->
            <div>
                <label for="batch" class="text-white">Select Batch:</label>
                <select id="batch" name="batch" class="p-2 rounded bg-gray-800 text-white" onchange="this.form.submit()">
                    @foreach ($batches as $batch)
                        <option value="{{ $batch->batchID }}" {{ $batch->batchID == $selectedBatch ? 'selected' : '' }}>
                            {{ $batch->batchName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Faculty Filter (only for admins) -->
            @if (auth()->user()->hasRole('admin'))
                <div>
                    <label for="faculty" class="text-white">Select Faculty:</label>
                    <select id="faculty" name="faculty" class="p-2 rounded bg-gray-800 text-white" onchange="this.form.submit()">
                        <option value="">All Faculties</option>
                        @foreach ($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ $faculty->id == $selectedFaculty ? 'selected' : '' }}>
                                {{ $faculty->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </form>

        <!-- Scrollable Table -->
        <div class="overflow-x-auto bg-gray-800 rounded-lg shadow-lg p-4">
            @if (!auth()->user()->hasRole('admin'))
                <form method="POST" action="{{ route('programs.update_entry_levels') }}">
                    @csrf
            @endif

            <table class="table-auto w-full border-collapse border border-gray-700 text-white relative">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="border border-gray-700 px-4 py-2 text-left sticky">Program Code</th>
                        <th class="border border-gray-700 px-4 py-2 text-left sticky">Program Name</th>
                        @foreach ($categories as $category)
                            <th class="border border-gray-700 px-4 py-2 text-center">{{ $category->categoryName }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($programs as $program)
                        <tr>
                            <td class="border border-gray-700 px-4 py-2 sticky">{{ $program->programID }}</td>
                            <td class="border border-gray-700 px-4 py-2 sticky">{{ $program->programName }}</td>
                            @foreach ($categories as $category)
                                <td class="border border-gray-700 px-4 py-2 text-center">
                                    <input 
                                        type="checkbox" 
                                        name="entry_levels[{{ $program->programID }}][{{ $category->entryLevelCategoryID }}]"
                                        value="1"
                                        {{ isset($programEntryLevels[$program->programID]) && $programEntryLevels[$program->programID]->contains('entry_level_category_id', $category->entryLevelCategoryID) ? 'checked' : '' }}
                                        {{ auth()->user()->hasRole('admin') ? 'disabled' : '' }}
                                    >
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if (!auth()->user()->hasRole('admin'))
                <div class="mt-4 text-right">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">Save All</button>
                </div>
                </form>
            @endif
        </div>
    </div>
        <!-- JavaScript -->
        <script>
        function toggleEditMode(event) {
            event.preventDefault();

            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            const saveButton = document.getElementById('saveButton');
            const editButton = document.getElementById('editButton');

            // Toggle between edit and view-only mode
            const isEditing = checkboxes[0].disabled === false;

            if (isEditing) {
                // Switch to view-only mode
                checkboxes.forEach((checkbox) => checkbox.setAttribute('disabled', true));
                saveButton.classList.add('hidden');
                editButton.textContent = 'Edit';
                editButton.classList.remove('bg-red-500');
                editButton.classList.add('bg-blue-500');
            } else {
                // Switch to edit mode
                checkboxes.forEach((checkbox) => checkbox.removeAttribute('disabled'));
                saveButton.classList.remove('hidden');
                editButton.textContent = 'Cancel Edit';
                editButton.classList.remove('bg-blue-500');
                editButton.classList.add('bg-red-500');
            }
        }
    </script>
</x-app-layout>


<style>
/* Ensure proper sticky positioning and alignment */
table th.sticky,
table td.sticky {
    position: sticky;
    z-index: 10;
    background-color: #1f2937; /* Consistent dark background */
    box-shadow: 2px 0px 5px rgba(0, 0, 0, 0.1); /* Optional shadow for separation */
}

/* Specific widths and positioning for sticky columns */
table th:first-child,
table td:first-child {
    width: 150px;
    left: 0;
    background-color: #1f2937; /* Ensure no see-through */
    z-index: 20; /* Keep above other content */
}

table th:nth-child(2),
table td:nth-child(2) {
    width: 250px;
    left: 121px;
    background-color: #1f2937; /* Ensure no see-through */
    z-index: 20; /* Keep above other content */
}

/* Adjust for border and shadows */
table {
    border-spacing: 0;
    border-collapse: collapse;
}


</style>