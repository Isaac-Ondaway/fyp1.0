<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Programs') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
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
                <div class="mt-7">
                            <a href="{{ route('programs.create') }}" class="btn btn-primary bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add New Program
                            </a>
                </div>
            </div>

            <!-- Program List -->
            <div id="programList" class="overflow-hidden shadow-lg rounded-lg bg-gray-900 p-4">
                <p class="text-gray-300">Please select a batch to view programs.</p>
            </div>
        </div>
    </div>

    <!-- Program Details Modal -->
    <div id="programModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-gray-900 rounded-lg shadow-lg max-w-2xl w-full p-6">
            <h3 class="text-2xl font-bold text-gray-200 mb-4">Program Details</h3>
            <div class="text-gray-300">
                <p><strong>Program ID:</strong> <span id="modalProgramID"></span></p>
                <p><strong>Program Name:</strong> <span id="modalProgramNameDetail"></span></p>
                <p><strong>Semester:</strong> <span id="modalProgramSem"></span></p>
                <p><strong>Education Level:</strong> <span id="modalLevelEdu"></span></p>
                <p><strong>NEC:</strong> <span id="modalNEC"></span></p>
                <p><strong>Program Fee:</strong> <span id="modalProgramFee"></span></p>
                <p><strong>Status:</strong> <span id="modalProgramStatus"></span></p>
                <p><strong>Description:</strong> <span id="modalProgramDesc"></span></p>
                <p><strong>Study Program:</strong> <span id="modalStudyProgram"></span></p>
                <p><strong>Interview Exam:</strong> <span id="modalIsInterviewExam"></span></p>
                <p><strong>Ujian Medsi:</strong> <span id="modalIsUjianMedsi"></span></p>
                <p><strong>Rayuan:</strong> <span id="modalIsRayuan"></span></p>
                <p><strong>Double Degree:</strong> <span id="modalIsDDegree"></span></p>
                <p><strong>Learning Module:</strong> <span id="modalLearnMod"></span></p>
                <p><strong>Bumiputera:</strong> <span id="modalIsBumiputera"></span></p>
                <p><strong>TVET:</strong> <span id="modalIsTVET"></span></p>
                <p><strong>Kompetitif:</strong> <span id="modalIsKompetitif"></span></p>
                <p><strong>BTECH:</strong> <span id="modalIsBTECH"></span></p>
                <p><strong>OKU:</strong> <span id="modalIsOKU"></span></p>
            </div>
            <button onclick="closeProgramModal()" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Close
            </button>
        </div>
    </div>

    <!-- JavaScript for AJAX and Modal Handling -->
    <script>
    document.getElementById('batchFilter').addEventListener('change', function() {
        const batchID = this.value;
        const programList = document.getElementById('programList');
        
        if (batchID) {
            // Show a loading message
            programList.innerHTML = '<p class="text-gray-300 p-4">Loading programs...</p>';
            
            // Fetch programs based on selected batch
            fetch(`/programs/batch/${batchID}`)
                .then(response => response.text())
                .then(html => {
                    if (html.trim() === "") {
                        // If the response is empty, display "No programs found" message
                        programList.innerHTML = '<p class="text-gray-300 p-4">No programs found for the selected batch.</p>';
                    } else {
                        // Otherwise, display the returned HTML
                        programList.innerHTML = html;
                    }
                })
                .catch(error => {
                    console.error('Error fetching programs:', error);
                    programList.innerHTML = '<p class="text-gray-300 p-4">Failed to load programs.</p>';
                });
        } else {
            // Clear the program list if no batch is selected
            programList.innerHTML = '<p class="text-gray-300">Please select a batch to view programs.</p>';
        }
    });

        function showProgramModal(program) {
            document.getElementById('modalProgramID').innerText = program.programID;
            document.getElementById('modalProgramNameDetail').innerText = program.programName;
            document.getElementById('modalProgramSem').innerText = program.programSem || 'N/A';
            document.getElementById('modalLevelEdu').innerText = program.levelEdu;
            document.getElementById('modalNEC').innerText = program.NEC || 'N/A';
            document.getElementById('modalProgramFee').innerText = program.programFee || 'N/A';
            document.getElementById('modalProgramStatus').innerText = program.programStatus;
            document.getElementById('modalProgramDesc').innerText = program.programDesc || 'N/A';
            document.getElementById('modalStudyProgram').innerText = program.studyProgram || 'N/A';

            document.getElementById('modalIsInterviewExam').innerText = program.isInterviewExam ? 'Yes' : 'No';
            document.getElementById('modalIsUjianMedsi').innerText = program.isUjianMedsi ? 'Yes' : 'No';
            document.getElementById('modalIsRayuan').innerText = program.isRayuan ? 'Yes' : 'No';
            document.getElementById('modalIsDDegree').innerText = program.isDDegree ? 'Yes' : 'No';
            document.getElementById('modalLearnMod').innerText = program.learnMod ? 'Yes' : 'No';
            document.getElementById('modalIsBumiputera').innerText = program.isBumiputera ? 'Yes' : 'No';
            document.getElementById('modalIsTVET').innerText = program.isTVET ? 'Yes' : 'No';
            document.getElementById('modalIsKompetitif').innerText = program.isKompetitif ? 'Yes' : 'No';
            document.getElementById('modalIsBTECH').innerText = program.isBTECH ? 'Yes' : 'No';
            document.getElementById('modalIsOKU').innerText = program.isOKU ? 'Yes' : 'No';

            document.getElementById('programModal').style.display = 'flex';
        }

        function closeProgramModal() {
            document.getElementById('programModal').style.display = 'none';
        }
    </script>
</x-app-layout>
