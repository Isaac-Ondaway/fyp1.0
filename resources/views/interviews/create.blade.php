<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Create Interview') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <!-- Flex container for Create Interview and Upload CSV Button -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-200">Create Interview</h1>
            
            <!-- Upload CSV Button -->
            <a href="{{ route('interviews.uploadCsv') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg">
                Upload CSV
            </a>
        </div>
        <!-- Create Interview Form -->
        <form action="{{ route('interviews.bulkStore') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Flex container for Program and Batch -->
            <div class="flex space-x-4">
                <!-- Select Program -->
                <div class="w-1/2">
                    <label for="programID" class="block text-gray-300 font-semibold mb-2">Select Program</label>
                    <select id="programID" name="programID" class="form-select w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">Choose a Program</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->programID }}">{{ $program->programName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Batch -->
                <div class="w-1/2">
                    <label for="batchID" class="block text-gray-300 font-semibold mb-2">Select Batch</label>
                    <select id="batchID" name="batchID" class="form-select w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">Choose a Batch</option>
                        <!-- Batches will be dynamically populated based on the selected program -->
                    </select>
                </div>
            </div>

            <!-- Dynamic Rows for Interviewees -->
            <div id="interviewees-container" class="space-y-4">
                <!-- Initial Interviewee Row -->
                <div class="interviewee-row bg-gray-800 p-4 rounded-lg" id="interviewee-0">
                    <div>
                        <label for="interviewees[0][intervieweeName]" class="block text-gray-300 font-semibold mb-2">Interviewee Name</label>
                        <input type="text" name="interviewees[0][intervieweeName]" placeholder="Interviewee Name" class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    
                    <div class="mt-4"></div>
                    
                    <div>
                        <label for="interviewees[0][contactNumber]" class="block text-gray-300 font-semibold mb-2">Contact Number</label>
                        <input type="text" name="interviewees[0][contactNumber]" placeholder="Contact Number" class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    
                    <div class="mt-4"></div>
                    
                    <div>
                        <label for="interviewees[0][email]" class="block text-gray-300 font-semibold mb-2">Email</label>
                        <input type="email" name="interviewees[0][email]" placeholder="Email Address" class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
                    </div>

                    <!-- Remove Button -->
                    <div class="mt-4">
                        <button type="button" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg" onclick="removeInterviewee('interviewee-0')">Remove</button>
                    </div>
                </div>
            </div>

            <!-- Button to Add More Rows -->
            <div>
                <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg mt-4" onclick="addInterviewee()">Add More Interviewee</button>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg">
                    Create Interviews
                </button>
            </div>
        </form>
    </div>

<!-- JavaScript to Dynamically Add and Remove Interviewee Rows -->
<script>
    let intervieweeCount = 1; // Start from 1 since 0 is already used

    // Function to Add More Interviewee Rows
    function addInterviewee() {
        const container = document.getElementById('interviewees-container');
        const newRow = document.createElement('div');
        newRow.className = 'interviewee-row bg-gray-800 p-4 rounded-lg mt-4';
        newRow.id = `interviewee-${intervieweeCount}`;

        newRow.innerHTML = `
            <div>
                <label for="interviewees[${intervieweeCount}][intervieweeName]" class="block text-gray-300 font-semibold mb-2">Interviewee Name</label>
                <input type="text" name="interviewees[${intervieweeCount}][intervieweeName]" placeholder="Interviewee Name" class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
            </div>

            <div class="mt-4"></div>

            <div>
                <label for="interviewees[${intervieweeCount}][contactNumber]" class="block text-gray-300 font-semibold mb-2">Contact Number</label>
                <input type="text" name="interviewees[${intervieweeCount}][contactNumber]" placeholder="Contact Number" class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
            </div>

            <div class="mt-4"></div>

            <div>
                <label for="interviewees[${intervieweeCount}][email]" class="block text-gray-300 font-semibold mb-2">Email</label>
                <input type="email" name="interviewees[${intervieweeCount}][email]" placeholder="Email Address" class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
            </div>

            <!-- Remove Button -->
            <div class="mt-4">
                <button type="button" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg" onclick="removeInterviewee('interviewee-${intervieweeCount}')">Remove</button>
            </div>
        `;

        container.appendChild(newRow);
        intervieweeCount++;
    }

    // Function to Remove Interviewee Rows
    function removeInterviewee(rowId) {
        const row = document.getElementById(rowId);
        row.remove();  // Remove the selected row from the form
    }
</script>

<script>
    document.getElementById('programID').addEventListener('change', function() {
        let programID = this.value;
        let batchSelect = document.getElementById('batchID');
        
        // Clear existing options
        batchSelect.innerHTML = '<option value="">Choose a Batch</option>';
        
        if (programID) {
            // Fetch batches based on selected program
            fetch(`/interviews/get-batches/${programID}`)
                .then(response => response.json())
                .then(batches => {
                    if (batches.length > 0) {
                        batches.forEach(batch => {
                            let option = document.createElement('option');
                            option.value = batch.batchID;
                            option.text = batch.batchName;
                            batchSelect.appendChild(option);
                        });
                    } else {
                        let noBatchOption = document.createElement('option');
                        noBatchOption.text = 'No batches available';
                        batchSelect.appendChild(noBatchOption);
                    }
                })
                .catch(error => console.error('Error fetching batches:', error));
        }
    });
</script>
</x-app-layout>
