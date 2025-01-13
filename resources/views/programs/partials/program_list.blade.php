<div class="mb-6">
    @if($programs->isNotEmpty())
        @foreach($programs as $facultyID => $batchesGroup)
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-100 bg-gray-600 p-3 rounded-t-lg">
                    {{$batchesGroup->first()?->first()?->faculty?->name ?? 'Faculty Not Found' }}
                </h2>

                @foreach($batchesGroup as $batchID => $programsInBatch)
                    <div class="bg-gray-800 p-4 rounded-lg mb-3">
                        <h3 class="text-md font-medium text-gray-200 bg-gray-700 p-2 rounded">
                            Batch: {{ $batches->firstWhere('batchID', $batchID)->batchName }}
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full mt-2 leading-normal text-left">
                                <thead>
                                    <tr class="bg-gray-700 text-gray-300 uppercase text-sm tracking-wider">
                                        <th class="py-2 px-4">Program ID</th>
                                        <th class="py-2 px-4">Program Name</th>
                                        <th class="py-2 px-4">Level of Education</th>
                                        <th class="py-2 px-4">Program Status</th>
                                        <!-- <th class="py-2 px-4 text-center">Actions</th> -->
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-800 text-gray-400">
                                    @foreach($programsInBatch as $program)
                                        <tr class="border-b border-gray-600 hover:bg-gray-700 cursor-pointer" onclick="showProgramModal({{ json_encode($program) }})">
                                            <td class="py-2 px-4">{{ $program->programID }}</td>
                                            <td class="py-2 px-4">{{ $program->programName }}</td>
                                            <td class="py-2 px-4">{{ $program->levelEdu }}</td>
                                            <td class="py-2 px-4">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $program->programStatus == 'Approved' ? 'bg-green-100 text-green-800' : ($program->programStatus == 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ ucfirst($program->programStatus) }}
                                                </span>
                                            </td>                        
                                            <!-- <td class="py-2 px-4 text-center">
                                                <a href="{{ route('programs.edit', ['programID' => $program->programID, 'batchID' => $program->batchID]) }}" 
                                                class="text-white bg-blue-500 hover:bg-blue-700 font-bold py-1 px-3 rounded-lg"
                                                onclick="event.stopPropagation()">Edit</a>
                                                <form action="{{ route('programs.destroy', ['programID' => $program->programID, 'batchID' => $program->batchID]) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this program?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-white bg-red-500 hover:bg-red-700 font-bold py-1 px-3 rounded-lg"
                                                    onclick="event.stopPropagation()">Delete</button>
                                                </form>
                                            </td> -->
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @else
        <p class="text-gray-300 p-4">No programs found for the selected batch.</p>
    @endif
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
                                                <a href="{{ route('programs.edit', ['programID' => $program->programID, 'batchID' => $program->batchID]) }}" 
                                                class="text-white bg-blue-500 hover:bg-blue-700 font-bold py-1 px-3 rounded-lg"
                                                onclick="event.stopPropagation()">Edit</a>
                                                <form action="{{ route('programs.destroy', ['programID' => $program->programID, 'batchID' => $program->batchID]) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this program?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-white bg-red-500 hover:bg-red-700 font-bold py-1 px-3 rounded-lg"
                                                    onclick="event.stopPropagation()">Delete</button>
                                                </form>
        </div>
    </div>
</div>



