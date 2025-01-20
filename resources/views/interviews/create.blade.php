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

                <!-- Select Batch -->
                <div class="w-1/2">
                    <label for="batchID" class="block text-gray-300 font-semibold mb-2">Select Batch</label>
                    <select id="batchID" name="batchID" class="form-select w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">Choose a Batch</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->batchID }}">{{ $batch->batchName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Program -->
                <div class="w-1/2">
                    <label for="programID" class="block text-gray-300 font-semibold mb-2">Select Program</label>
                    <select id="programID" name="programID" class="form-select w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">Choose a Program</option>
                        <!-- Programs will be dynamically populated based on the selected batch -->
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
        <input type="text" name="interviewees[0][contactNumber]" placeholder="Contact Number" class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500 contact-input" data-row-id="0" required>
        <span class="text-red-500 text-sm hidden" id="contact-error-0"></span>
    </div>
    
    <div class="mt-4"></div>
    
    <div>
        <label for="interviewees[0][email]" class="block text-gray-300 font-semibold mb-2">Email</label>
        <input type="email" name="interviewees[0][email]" placeholder="Email Address" class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500 email-input" data-row-id="0" required>
        <span class="text-red-500 text-sm hidden" id="email-error-0"></span>
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
                <input type="text" name="interviewees[${intervieweeCount}][contactNumber]" placeholder="Contact Number" class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500 contact-input" data-row-id="${intervieweeCount}" required>
                <span class="text-red-500 text-sm hidden" id="contact-error-${intervieweeCount}"></span>
            </div>

            <div class="mt-4"></div>

            <div>
                <label for="interviewees[${intervieweeCount}][email]" class="block text-gray-300 font-semibold mb-2">Email</label>
                <input type="email" name="interviewees[${intervieweeCount}][email]" placeholder="Email Address" class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500 email-input" data-row-id="${intervieweeCount}" required>
                <span class="text-red-500 text-sm hidden" id="email-error-${intervieweeCount}"></span>
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
        row.remove();
    }

    // Event Listeners for Input Validation
    document.addEventListener('input', function (event) {
        if (event.target.classList.contains('contact-input')) {
            checkForDuplicateContact(event.target);
        }
        if (event.target.classList.contains('email-input')) {
            checkForDuplicateEmail(event.target);
        }
    });

    // AJAX Check for Duplicate Contact Number
    function checkForDuplicateContact(input) {
    const contactNumber = input.value;
    const rowId = input.dataset.rowId;

    // Get the selected batchID and programID
    const batchID = document.getElementById('batchID').value;
    const programID = document.getElementById('programID').value;

    // Check if batchID and programID are selected
    if (!batchID || !programID) {
        alert('Please select a batch and a program before entering contact details.');
        return;
    }

    // Check for duplicates within the form
    let isDuplicateInForm = false;
    document.querySelectorAll('.contact-input').forEach(contactInput => {
        if (contactInput.dataset.rowId !== rowId && contactInput.value === contactNumber) {
            isDuplicateInForm = true;
        }
    });

    // Show duplicate message for form-level duplication
    const errorSpan = document.getElementById(`contact-error-${rowId}`);
    if (isDuplicateInForm) {
        errorSpan.textContent = 'Duplicate contact number in the form.';
        errorSpan.classList.remove('hidden');
        return;
    } else {
        errorSpan.classList.add('hidden');
    }

    // AJAX to check for database duplication
    fetch(`/interviews/check-duplicate-contact?contactNumber=${contactNumber}&batchID=${batchID}&programID=${programID}`)
        .then(response => response.json())
        .then(data => {
            if (data.isDuplicate) {
                errorSpan.textContent = 'This contact number already exists for the selected batch and program.';
                errorSpan.classList.remove('hidden');
            } else {
                errorSpan.classList.add('hidden');
            }
        })
        .catch(error => console.error('Error checking contact number:', error));
}


    // AJAX Check for Duplicate Email
    function checkForDuplicateEmail(input) {
    const email = input.value;
    const rowId = input.dataset.rowId;

    // Get the selected batchID and programID
    const batchID = document.getElementById('batchID').value;
    const programID = document.getElementById('programID').value;

    // Check if batchID and programID are selected
    if (!batchID || !programID) {
        alert('Please select a batch and a program before entering email details.');
        return;
    }

    // Check for duplicates within the form
    let isDuplicateInForm = false;
    document.querySelectorAll('.email-input').forEach(emailInput => {
        if (emailInput.dataset.rowId !== rowId && emailInput.value === email) {
            isDuplicateInForm = true;
        }
    });

    // Show duplicate message for form-level duplication
    const errorSpan = document.getElementById(`email-error-${rowId}`);
    if (isDuplicateInForm) {
        errorSpan.textContent = 'Duplicate email in the form.';
        errorSpan.classList.remove('hidden');
        return;
    } else {
        errorSpan.classList.add('hidden');
    }

    // AJAX to check for database duplication
    fetch(`/interviews/check-duplicate-email?email=${email}&batchID=${batchID}&programID=${programID}`)
        .then(response => response.json())
        .then(data => {
            if (data.isDuplicate) {
                errorSpan.textContent = 'This email already exists for the selected batch and program.';
                errorSpan.classList.remove('hidden');
            } else {
                errorSpan.classList.add('hidden');
            }
        })
        .catch(error => console.error('Error checking email:', error));
}


    // Prevent Form Submission if Batch and Program are not Selected or Errors Exist
    document.querySelector('form').addEventListener('submit', function (event) {
        const batchID = document.getElementById('batchID').value;
        const programID = document.getElementById('programID').value;

        if (!batchID || !programID) {
            alert('Please select a batch and a program.');
            event.preventDefault();
            return;
        }

        const hasErrors = Array.from(document.querySelectorAll('.text-red-500')).some(errorSpan => !errorSpan.classList.contains('hidden'));
        if (hasErrors) {
            alert('Please fix all duplicate issues before submitting.');
            event.preventDefault();
        }
    });
</script>


<script>
        document.getElementById('batchID').addEventListener('change', function() {
            let batchID = this.value;
            let programSelect = document.getElementById('programID');
            
            // Clear existing options
            programSelect.innerHTML = '<option value="">Choose a Program</option>';
            
            if (batchID) {
                // Fetch programs based on selected batch
                fetch(`/interviews/get-programs/${batchID}`)
                    .then(response => response.json())
                    .then(programs => {
                        if (programs.length > 0) {
                            programs.forEach(program => {
                                let option = document.createElement('option');
                                option.value = program.programID;
                                option.text = program.programName;
                                programSelect.appendChild(option);
                            });
                        } else {
                            let noProgramOption = document.createElement('option');
                            noProgramOption.text = 'No programs available';
                            programSelect.appendChild(noProgramOption);
                        }
                    })
                    .catch(error => console.error('Error fetching programs:', error));
            }
        });
    </script>

</x-app-layout>
