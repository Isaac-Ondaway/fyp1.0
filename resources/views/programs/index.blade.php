<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Programs') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-extrabold mb-8 text-gray-200">Program List</h1>

        <!-- Batch Selection -->
        <div class="flex items-center mb-4 space-x-4">
            <div class="w-48">
                <label for="batchFilter" class="block text-gray-300 font-semibold mb-2">Select Batch:</label>
                <select id="batchFilter" name="batchID" class="form-select w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Choose a Batch</option>
                    @foreach($batches as $batch)
                        <option value="{{ $batch->batchID }}">{{ $batch->batchName }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Program List -->
        <div id="programList" class="overflow-hidden shadow-lg rounded-lg bg-gray-900">
            <p class="text-gray-300">Please select a batch to view programs.</p>
        </div>
    </div>

    <!-- Add JavaScript for AJAX -->
    <script>
        document.getElementById('batchFilter').addEventListener('change', function() {
            const batchID = this.value;
            const programList = document.getElementById('programList');
            
            if (batchID) {
                // Show a loading message
                programList.innerHTML = '<p class="text-gray-300 p-4">Loading programs...</p>';
                
                // Fetch programs based on selected batch
                fetch(`/programs/batch/${batchID}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(html => {
                        programList.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error fetching programs:', error);
                        programList.innerHTML = '<p class="text-gray-300 p-4">Failed to load programs.</p>';
                    });
            } else {
                // Clear the program list if no batch is selected
                programList.innerHTML = '';
            }
        });

        function showProgramModal(program) {
        // Basic Details
        document.getElementById('modalProgramID').innerText = program.programID;
        document.getElementById('modalProgramNameDetail').innerText = program.programName;
        document.getElementById('modalProgramSem').innerText = program.programSem || 'N/A';
        document.getElementById('modalLevelEdu').innerText = program.levelEdu;
        document.getElementById('modalNEC').innerText = program.NEC || 'N/A';
        document.getElementById('modalProgramFee').innerText = program.programFee || 'N/A';
        document.getElementById('modalProgramStatus').innerText = program.programStatus;
        document.getElementById('modalProgramDesc').innerText = program.programDesc || 'N/A';
        document.getElementById('modalStudyProgram').innerText = program.studyProgram || 'N/A';

        // Additional Boolean Fields (display Yes/No)
        document.getElementById('modalIsInterviewExam').innerText = program.isInterviewExam ? 'Yes' : 'No';
        document.getElementById('modalIsUjianMedsi').innerText = program.isUjianMedsi ? 'Yes' : 'No';
        document.getElementById('modalIsRayuan').innerText = program.isRayuan ? 'Yes' : 'No';
        document.getElementById('modalIsDDegree').innerText = program.isDDegree ? 'Yes' : 'No';
        document.getElementById('modalLearnMod').innerText = program.learnMod ? 'Yes' : 'No';
        document.getElementById('modalIsBumiputera').innerText = program.isBumiputera ? 'Yes' : 'No';
        document.getElementById('modalIsTEVT').innerText = program.isTEVT ? 'Yes' : 'No';
        document.getElementById('modalIsKompetitif').innerText = program.isKompetitif ? 'Yes' : 'No';
        document.getElementById('modalIsBTECH').innerText = program.isBTECH ? 'Yes' : 'No';
        document.getElementById('modalIsOKU').innerText = program.isOKU ? 'Yes' : 'No';

        // Show modal
        document.getElementById('programModal').style.display = 'flex';
    }

    function closeProgramModal() {
        document.getElementById('programModal').style.display = 'none';
    }
    </script>
</x-app-layout>
