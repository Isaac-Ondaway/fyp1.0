<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Interviews') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-extrabold mb-8 text-gray-200">Interview List</h1>

        <!-- Filter Section -->
        <form method="GET" action="{{ route('interviews.index') }}">
            <div class="flex items-center mb-4 space-x-4">
                <div class="w-48">
                    <label for="batchFilter" class="block text-gray-300 font-semibold mb-2">Filter by Batch:</label>
                    <select id="batchFilter" name="batchID" class="form-select w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Batches</option>
                        @foreach($batches as $batch)
                        <option value="{{ $batch->batchID }}" {{ request('batchID') == $batch->batchID ? 'selected' : '' }}>
                            {{ $batch->batchName }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-48">
                    <label for="programFilter" class="block text-gray-300 font-semibold mb-2">Filter by Program:</label>
                    <select id="programFilter" name="programID" class="form-select w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Programs</option>
                        @foreach($programs as $program)
                        <option value="{{ $program->programID }}" {{ request('programID') == $program->programID ? 'selected' : '' }}>
                            {{ $program->programName }}
                        </option>
                        @endforeach
                    </select>
                </div>


                <div class="flex space-x-4 mt-6">
                    <!-- Apply Filters Button -->
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                        Apply Filters
                    </button>
                </div>
            </div>
        </form>

        <!-- Section for Adding Interviewees or via CSV -->
        @if(Auth::user()->hasRole('admin'))
        <div class="flex justify-between items-center mb-8">
            <!-- Button to Add Interview (Dynamic Rows) -->
            <a href="{{ route('interviews.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg">
                Add Interview
            </a>
            </form>

        </div>
        @endif

        <!-- Interview List -->
        <form action="{{ route('interviews.bulkUpdate') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4 text-white font-bold py-2 px-6">
                <input type="checkbox" id="select-all" class="mr-2"> Select All
            </div>

            <div class="overflow-hidden shadow-lg rounded-lg bg-gray-900 mx-auto max-w-full">
                @if($interviews->count() > 0)
                @foreach($interviews->groupBy(['programID', 'batchID']) as $groupedInterviewsByProgram)
                @foreach($groupedInterviewsByProgram as $groupedInterviews)
                @php
                $firstInterview = $groupedInterviews->first();
                $program = $firstInterview->program;
                $batch = $firstInterview->program->batch;
                @endphp

                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-100 bg-gray-600 p-3 rounded-t-lg">
                        {{ $program->programName }}
                    </h2>
                    <div class="bg-gray-800 p-4 rounded-lg mb-3">
                        <h3 class="text-md font-medium text-gray-200 bg-gray-700 p-2 rounded">
                            Batch: {{ $batch->batchName ?? 'N/A' }}
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full mt-2 leading-normal text-left">
                                <thead>
                                    <tr class="bg-gray-700 text-gray-300 uppercase text-sm tracking-wider">
                                        <th class="py-2 px-4">Select</th>
                                        <th class="py-2 px-4">Interviewee Name</th>
                                        <th class="py-2 px-4">Contact Number</th>
                                        <th class="py-2 px-4">Program ID</th>
                                        <th class="py-2 px-4">Interview Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-800 text-gray-400">
                                    @foreach($groupedInterviews as $interview)
                                    <tr class="border-b border-gray-600 hover:bg-gray-700">
                                        <td class="py-2 px-4">
                                            <div class="flex items-center justify-center" style="margin-left: -100px;">
                                                <input type="checkbox" name="interview_ids[]" value="{{ $interview->interviewID }}">
                                            </div>
                                        </td>
                                        <td class="py-2 px-4">{{ $interview->intervieweeName }}</td>
                                        <td class="py-2 px-4">{{ $interview->contactNumber }}</td>
                                        <td class="py-2 px-4">{{ $interview->programID }}</td>
                                        <td class="py-2 px-4">{{ $interview->interviewStatus }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach
                @endforeach
                @else
                <p class="text-gray-200">No interviews found for the selected batch or programs.</p>
                @endif

                <!-- Dropdown for selecting new status, outside the loop -->
                <div class="w-40">
                    <label for="newStatus">Change Status To:</label>
                    <select name="newStatus" id="newStatus" class="form-select w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                        <option value="Pending">Pending</option>
                        <option value="Scheduled">Scheduled</option>
                        <option value="Completed">Completed</option>
                        <option value="Canceled">Canceled</option>
                    </select>
                </div>

                <!-- Submit button for bulk update -->
                <div class="mt-4">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700">
                        Update Selected
                    </button>
                </div>
            </div>
        </form>

        <!-- Separate form for delete action -->
        <form id="deleteForm" action="{{ route('interviews.bulkDelete') }}" method="POST">
            @csrf
            @method('DELETE')
            <!-- Pass the selected interview IDs -->
            <input type="hidden" name="interview_ids[]" id="delete_interview_ids">

            <div class="mt-4">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700">
                    Delete Selected
                </button>
            </div>
        </form>

        <script>
            const selectAll = document.getElementById('select-all');
            if (selectAll) {
                selectAll.addEventListener('click', function(event) {
                    let checkboxes = document.querySelectorAll('input[name="interview_ids[]"]');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = event.target.checked;
                    });
                });
            }

            // Copy selected interview IDs into delete form when submitting the delete action
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.addEventListener('submit', function(event) {
                let selectedIds = Array.from(document.querySelectorAll('input[name="interview_ids[]"]:checked'))
                    .map(cb => cb.value);

                document.getElementById('delete_interview_ids').value = selectedIds;
            });
        </script>


        <script>
            // Select all checkboxes when clicking "select all"
            document.getElementById('select-all').addEventListener('click', function(event) {
                let checkboxes = document.querySelectorAll('input[name="interview_ids[]"]');
                for (let checkbox of checkboxes) {
                    checkbox.checked = event.target.checked;
                }
            });
        </script>

        <script>
            document.getElementById('bulk-update-btn').addEventListener('click', function() {
                const formData = new FormData(document.getElementById('bulk-update-form'));

                // Log form data for debugging
                for (var pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }

                // AJAX request to bulk update
                fetch('{{ route('interviews.bulkUpdateAjax') }}', {  // Fixed the route here
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Statuses updated successfully');
                        location.reload(); // Optionally reload the page to reflect the updated statuses
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const programSelect = document.getElementById('programFilter');
                const batchSelect = document.getElementById('batchFilter');

                // Listen for program selection and fetch batches
                programSelect.addEventListener('change', function() {
                    const programID = this.value;

                    if (programID) {
                        fetch(`/interviews/get-batches-for-program/${programID}`)
                            .then(response => response.json())
                            .then(batches => {
                                batchSelect.innerHTML = '<option value="">All Batches</option>';
                                batches.forEach(batch => {
                                    const option = document.createElement('option');
                                    option.value = batch.batchID;
                                    option.text = batch.batchName;
                                    batchSelect.appendChild(option);
                                });
                            });
                    } else {
                        // Reset batch list if no program is selected
                        resetBatchList();
                    }
                });

                // Listen for batch selection and fetch programs
                batchSelect.addEventListener('change', function() {
                    const batchID = this.value;

                    if (batchID) {
                        fetch(`/interviews/get-programs-for-batch/${batchID}`)
                            .then(response => response.json())
                            .then(programs => {
                                programSelect.innerHTML = '<option value="">All Programs</option>';
                                programs.forEach(program => {
                                    const option = document.createElement('option');
                                    option.value = program.programID;
                                    option.text = program.programName;
                                    programSelect.appendChild(option);
                                });
                            });
                    } else {
                        // Reset program list if no batch is selected
                        resetProgramList();
                    }
                });

                // Reset the batch list
                function resetBatchList() {
                    batchSelect.innerHTML = '<option value="">All Batches</option>';
                }

                // Reset the program list
                function resetProgramList() {
                    programSelect.innerHTML = '<option value="">All Programs</option>';
                }
            });
        </script>

</x-app-layout>