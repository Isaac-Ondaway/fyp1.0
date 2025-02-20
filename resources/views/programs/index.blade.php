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

                @if(Auth::user()->hasRole('admin'))
                    <!-- Faculty Filter -->
                    <div class="w-65">
                        <label for="facultyFilter" class="block text-gray-300 font-semibold mb-2">Select Faculty:</label>
                        <select id="facultyFilter" name="facultyID" class="form-select w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Faculty</option>
                            @foreach($faculties as $faculty)
                                <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @else
    <input type="hidden" id="facultyFilter" value="{{ $facultyID }}">
                @endif

                <div class="mt-7">
                @if (!auth()->user()->hasRole('admin'))
                    <a href="{{ route('programs.create') }}" class="btn btn-primary bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add New Program
                    </a>
                @endif
                </div>
            </div>

            <!-- Program List -->
            <div id="programList" class="overflow-hidden shadow-lg rounded-lg bg-gray-900 p-4">
                <p class="text-gray-300">Please select a batch to view programs.</p>
            </div>
        </div>
    </div>

    <!-- Program Details Modal -->
<div id="programModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden overflow-y-auto">
    <div class="bg-white p-8 rounded-lg shadow-lg w-3/4 md:w-1/2 max-h-[90vh] overflow-y-auto relative">
        <!-- Close Button at Top Right -->
        <button onclick="closeProgramModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 z-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <h2 class="text-3xl font-bold mb-6 text-gray-800 border-b pb-2">Program Details</h2>
        
        <!-- Basic Details Card -->
        <div class="bg-gray-100 p-4 rounded-md shadow-sm mb-4">
            <h3 class="text-xl font-semibold text-gray-700 mb-3">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <p><strong>Program ID:</strong> <span id="modalProgramID"></span></p>
                <p><strong>Program Name:</strong> <span id="modalProgramNameDetail"></span></p>
                <p><strong>Total Semesters:</strong> <span id="modalProgramSem"></span></p>
                <p><strong>Level of Education:</strong> <span id="modalLevelEdu"></span></p>
                <p><strong>NEC:</strong> <span id="modalNEC"></span></p>
                <p><strong>Program Status:</strong> <span id="modalProgramStatus"></span></p>
                <p><strong>Study Program:</strong> <span id="modalStudyProgram"></span></p>
            </div>
        </div>

        <!-- Fee and Description Card -->
        <div class="bg-gray-100 p-4 rounded-md shadow-sm mb-4">
            <h3 class="text-xl font-semibold text-gray-700 mb-3">Program Fee and Description</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p><strong>Program Fee:</strong></p>
                    <p id="modalProgramFee" class="text-gray-600"></p>
                </div>
                <div>
                    <p><strong>Description:</strong></p>
                    <p id="modalProgramDesc" class="text-gray-600"></p>
                </div>
            </div>
        </div>

        <!-- Criteria Card -->
        <div class="bg-gray-100 p-4 rounded-md shadow-sm mb-4">
            <h3 class="text-xl font-semibold text-gray-700 mb-3">Additional Criteria</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <p><strong>Interview Exam:</strong> <span id="modalIsInterviewExam" class="font-semibold"></span></p>
                <p><strong>Ujian Medsi:</strong> <span id="modalIsUjianMedsi" class="font-semibold"></span></p>
                <p><strong>Rayuan:</strong> <span id="modalIsRayuan" class="font-semibold"></span></p>
                <p><strong>DDegree:</strong> <span id="modalIsDDegree" class="font-semibold"></span></p>
                <p><strong>Learn Mode:</strong> <span id="modalLearnMod" class="font-semibold"></span></p>
            </div>
        </div>

        <!-- Eligibility Card -->
        <div class="bg-gray-100 p-4 rounded-md shadow-sm mb-6">
            <h3 class="text-xl font-semibold text-gray-700 mb-3">Eligibility</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <p><strong>Bumiputera:</strong> <span id="modalIsBumiputera" class="font-semibold"></span></p>
                <p><strong>TVET:</strong> <span id="modalIsTVET" class="font-semibold"></span></p>
                <p><strong>Kompetitif:</strong> <span id="modalIsKompetitif" class="font-semibold"></span></p>
                <p><strong>BTECH:</strong> <span id="modalIsBTECH" class="font-semibold"></span></p>
                <p><strong>OKU:</strong> <span id="modalIsOKU" class="font-semibold"></span></p>
            </div>
        </div>

        <!-- Bottom Close Button -->
        <div class="text-right">

                                                <a href="#" id="editButton" 
                                                    class="text-white bg-blue-500 hover:bg-blue-700 font-bold py-1 px-3 rounded-lg"
                                                    onclick="event.stopPropagation()">Edit</a>

                                                    <form id="deleteForm" action="#" method="POST" class="inline-block" 
                                                        onsubmit="return confirm('Are you sure you want to delete this program?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="text-white bg-red-500 hover:bg-red-700 font-bold py-1 px-3 rounded-lg"
                                                                onclick="event.stopPropagation()">Delete</button>
                                                    </form>

        </div>
    </div>
</div>


    <!-- JavaScript for AJAX and Modal Handling -->
    <script>
document.getElementById('batchFilter').addEventListener('change', fetchFilteredPrograms);
document.getElementById('facultyFilter').addEventListener('change', fetchFilteredPrograms);

function fetchFilteredPrograms() {
    const batchFilter = document.getElementById('batchFilter');
    const facultyFilter = document.getElementById('facultyFilter');
    const programList = document.getElementById('programList');

    if (!programList) {
        console.error('Program list container not found.');
        return;
    }

    if (facultyFilter) {
        facultyFilter.addEventListener('change', fetchFilteredPrograms);
    }

    // Get filter values
    const batchID = batchFilter ? batchFilter.value : '';
    const facultyID = facultyFilter ? facultyFilter.value : '';

    // Show a loading message
    programList.innerHTML = '<p class="text-gray-300 p-4">Loading programs...</p>';

    // Build query parameters
    const params = new URLSearchParams();
    if (batchID) params.append('batchID', batchID);
    if (facultyID) params.append('facultyID', facultyID);

    // Fetch programs based on selected filters
    fetch(`/programs/filter?${params.toString()}`)
        .then(response => response.text())
        .then(html => {
            if (html.trim() === '') {
                programList.innerHTML = '<p class="text-gray-300 p-4">No programs found for the selected filters.</p>';
            } else {
                programList.innerHTML = html;
            }
        })
        .catch(error => {
            console.error('Error fetching programs:', error);
            programList.innerHTML = '<p class="text-gray-300 p-4">Failed to load programs.</p>';
        });
}



    function showProgramModal(program) {
    // Populate modal fields
    document.getElementById('modalProgramID').textContent = program.programID || 'N/A';
    document.getElementById('modalProgramNameDetail').textContent = program.programName || 'N/A';
    document.getElementById('modalProgramSem').textContent = program.programSem || 'N/A';
    document.getElementById('modalLevelEdu').textContent = program.levelEdu || 'N/A';
    document.getElementById('modalNEC').textContent = program.NEC || 'N/A';
    document.getElementById('modalProgramFee').textContent = program.programFee || 'N/A';
    document.getElementById('modalProgramStatus').textContent = program.programStatus || 'N/A';
    document.getElementById('modalProgramDesc').textContent = program.programDesc || 'N/A';
    document.getElementById('modalStudyProgram').textContent = program.studyProgram || 'N/A';

    document.getElementById('modalIsInterviewExam').textContent = program.isInterviewExam ? 'Yes' : 'No';
    document.getElementById('modalIsUjianMedsi').textContent = program.isUjianMedsi ? 'Yes' : 'No';
    document.getElementById('modalIsRayuan').textContent = program.isRayuan ? 'Yes' : 'No';
    document.getElementById('modalIsDDegree').textContent = program.isDDegree ? 'Yes' : 'No';
    document.getElementById('modalLearnMod').textContent = program.learnMod ? 'Yes' : 'No';
    document.getElementById('modalIsBumiputera').textContent = program.isBumiputera ? 'Yes' : 'No';
    document.getElementById('modalIsTVET').textContent = program.isTVET ? 'Yes' : 'No';
    document.getElementById('modalIsKompetitif').textContent = program.isKompetitif ? 'Yes' : 'No';
    document.getElementById('modalIsBTECH').textContent = program.isBTECH ? 'Yes' : 'No';
    document.getElementById('modalIsOKU').textContent = program.isOKU ? 'Yes' : 'No';

    // Update Edit button href
    const editButton = document.getElementById('editButton');
    if (editButton) {
        editButton.setAttribute('href', `/programs/${program.programID}/${program.batchID}/edit`);
    } else {
        console.error('Edit button not found.');
    }

    // Update Delete form action
    const deleteForm = document.getElementById('deleteForm');
    if (deleteForm) {
        deleteForm.setAttribute('action', `/programs/${program.programID}/${program.batchID}`);
    } else {
        console.error('Delete form not found.');
    }

    // Show the modal
    document.getElementById('programModal').classList.remove('hidden');
}

function closeProgramModal() {
    document.getElementById('programModal').classList.add('hidden');
}

    </script>
</x-app-layout>
