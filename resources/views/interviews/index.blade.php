<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Interviews') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <h3 class="text-xl font-semibold text-gray-100 mb-6">Interviewee List</h3>

        <!-- Filter Section -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <!-- Filter by Batch -->
                <div>
                    <label for="batchFilter" class="block text-gray-300 font-semibold mb-2">Filter by Batch:</label>
                    <select id="batchFilter" name="batchID" class="form-select w-full rounded-md bg-gray-700 text-gray-100 px-4 py-2">
                        <option value="">All Batches</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->batchID }}">{{ $batch->batchName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter by Faculty -->
                @if(Auth::user()->hasRole('admin'))
                <div class="w-65">
                    <label for="facultyFilter" class="block text-gray-300 font-semibold mb-2">Filter by Faculty:</label>
                    <select id="facultyFilter" name="faculty_id" class="form-select w-full rounded-md bg-gray-700 text-gray-100 px-4 py-2">
                        <option value="">All Faculties</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ $facultyID == $faculty->id ? 'selected' : '' }}>{{ $faculty->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                    <!-- Search Box -->
                    <div class="w-64">
                        <label for="search" class="block text-gray-300 font-semibold mb-2">Search Interview:</label>
                        <input type="text" id="search" 
                            class="form-input w-full rounded-md bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" 
                            placeholder="Search by Name, Program, or Contact...">
                    </div>


                <!-- Add Interviewee Button -->
                <div class="ml-auto">
                    <a href="{{ route('interviews.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg">
                        Add Interviewee
                    </a>
                </div>
            </div>
        </div>

        <!-- Interview List Container -->
        <div id="interview-list">
            <!-- This is where the interview list will be dynamically loaded -->
            <p class="text-gray-200 text-lg mt-8">Please select a batch to view the interviewees.</p>
        </div>
    </div>

    <!-- Interview Details Modal -->
<div id="interviewModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden overflow-y-auto">
    <div class="bg-white p-8 rounded-lg shadow-lg w-3/4 md:w-1/2 max-h-[90vh] overflow-y-auto relative">
        <!-- Close Button at Top Right -->
        <button onclick="closeInterviewModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 z-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <h2 class="text-3xl font-bold mb-6 text-gray-800 border-b pb-2">Edit Interviewee Details</h2>

        <!-- Form to Edit Interviewee Details -->
        <form id="editInterviewForm" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-4 mb-6">
                <input type="hidden" id="modalIntervieweeID" name="id">

                <div>
                    <label for="modalIntervieweeName" class="block font-semibold text-gray-700 mb-2">Name</label>
                    <input type="text" id="modalIntervieweeName" name="name" class="w-full p-2 border rounded-md" required>
                </div>

                <div>
                    <label for="modalIntervieweeContact" class="block font-semibold text-gray-700 mb-2">Contact Number</label>
                    <input type="text" id="modalIntervieweeContact" name="contact_number" class="w-full p-2 border rounded-md" required>
                </div>

                <div>
                    <label for="modalIntervieweeEmail" class="block font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" id="modalIntervieweeEmail" name="email" class="w-full p-2 border rounded-md" required>
                </div>
            </div>

            <!-- Save Changes Button -->
            <div class="text-right">
                <button type="button" onclick="closeInterviewModal()" class="bg-gray-400 text-white py-2 px-4 rounded-md mr-2">Close</button>
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>


    
  <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search');
            const batchFilter = document.getElementById('batchFilter');
            const facultyFilter = document.getElementById('facultyFilter');
            const interviewList = document.getElementById('interview-list');

            function fetchInterviews() {
                const searchQuery = searchInput.value;
                const batchID = batchFilter ? batchFilter.value : '';
                const facultyID = facultyFilter ? facultyFilter.value : '';

                // Perform AJAX request to fetch filtered interviews
                fetch(`{{ route('interviews.index') }}?search=${searchQuery}&batchID=${batchID}&faculty_id=${facultyID}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(data => {
                    // Update the interview list container with the fetched HTML
                    interviewList.innerHTML = data;
                })
                .catch(error => {
                    console.error('Error fetching interview data:', error);
                });
            }

            // Fetch interviews when the search input changes
            searchInput.addEventListener('keyup', fetchInterviews);

            // Also fetch interviews when batch or faculty filters are changed
            if (batchFilter) batchFilter.addEventListener('change', fetchInterviews);
            if (facultyFilter) facultyFilter.addEventListener('change', fetchInterviews);

        });
    </script>

<script>
        // Open the modal and populate fields
        function openInterviewModal(interviewee) {
            console.log('Opening modal for:', interviewee);

            // Populate modal fields
            document.getElementById('modalIntervieweeID').value = interviewee.id;
            document.getElementById('modalIntervieweeName').value = interviewee.name;
            document.getElementById('modalIntervieweeContact').value = interviewee.contact_number;
            document.getElementById('modalIntervieweeEmail').value = interviewee.email;

            // Show the modal
            document.getElementById('interviewModal').classList.remove('hidden');
        }

        // Close the modal
        function closeInterviewModal() {
            document.getElementById('interviewModal').classList.add('hidden');
        }

        // Handle form submission
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('editInterviewForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const intervieweeID = document.getElementById('modalIntervieweeID').value;
                    const formData = new FormData(form);

                    fetch(`/interviews/${intervieweeID}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Interviewee updated successfully.');
                            closeInterviewModal();
                            location.reload();
                        } else {
                            alert('Failed to update interviewee.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            } else {
                console.error('Edit form not found.');
            }
        });
    </script>

</x-app-layout>
