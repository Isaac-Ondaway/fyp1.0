<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Program') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6 text-gray-200">Edit Program</h1>
        <form action="{{ route('programs.update', ['programID' => $program->programID, 'batchID' => $program->batchID]) }}" method="POST">
            @csrf
            @method('PATCH')

            <!-- Basic Information -->
            <!-- Program ID -->
            <div class="mb-4">
                <label for="programID" class="block text-gray-300 font-bold mb-2">Program ID:</label>
                <input type="text" id="programID" name="programID"
                    value="{{ old('programID', $program->programID) }}"
                    class="form-input rounded-md shadow-sm w-full bg-gray-700 text-gray-100"
                    {{ Auth::user()->hasRole('admin') ? 'disabled' : '' }} required>
                @if(Auth::user()->hasRole('admin'))
                    <input type="hidden" name="programID" value="{{ $program->programID }}">
                @endif
            </div>

            <!-- Batch ID -->
            <div class="mb-4">
                <label for="batchID" class="block text-gray-300 font-bold mb-2">Batch ID:</label>
                <input type="text" id="batchID" name="batchID"
                    value="{{ old('batchID', $program->batchID) }}"
                    class="form-input rounded-md shadow-sm w-full bg-gray-700 text-gray-100"
                    {{ Auth::user()->hasRole('admin') ? 'disabled' : '' }} required>
                @if(Auth::user()->hasRole('admin'))
                    <input type="hidden" name="batchID" value="{{ $program->batchID }}">
                @endif
            </div>

            <div class="mb-4">
                <label for="facultyName" class="block text-gray-300 font-bold mb-2">Faculty Name:</label>
                <input type="text" id="facultyName" name="facultyName" value="{{ old('facultyName', $program->faculty->name) }}" class="form-input rounded-md shadow-sm w-full bg-gray-700 text-gray-100" readonly>
            </div>

            <!-- Program Name -->
            <div class="mb-4">
                <label for="programName" class="block text-gray-300 font-bold mb-2">Program Name:</label>
                <input type="text" id="programName" name="programName"
                    value="{{ old('programName', $program->programName) }}"
                    class="form-input rounded-md shadow-sm w-full bg-gray-700 text-gray-100"
                    {{ Auth::user()->hasRole('admin') ? 'disabled' : '' }} required>
                @if(Auth::user()->hasRole('admin'))
                    <input type="hidden" name="programName" value="{{ $program->programName }}">
                @endif
            </div>

            <div class="mb-4">
                <label for="studyProgram" class="block text-gray-300 font-bold mb-2">Study Program:</label>
                <input type="text" id="studyProgram" name="studyProgram" value="{{ old('studyProgram', $program->studyProgram) }}" class="form-input rounded-md shadow-sm w-full bg-gray-700 text-gray-100" required>
            </div>

            <!-- Program Sem -->
            <div class="mb-4">
                <label for="programSem" class="block text-gray-300 font-bold mb-2">Total Semesters:</label>
                <input type="number" id="programSem" name="programSem"
                    value="{{ old('programSem', $program->programSem) }}"
                    class="form-input rounded-md shadow-sm w-full bg-gray-700 text-gray-100"
                    {{ Auth::user()->hasRole('admin') ? 'disabled' : '' }} required>
                @if(Auth::user()->hasRole('admin'))
                    <input type="hidden" name="programSem" value="{{ $program->programSem }}">
                @endif
            </div>

            <div class="mb-4">
                <label for="levelEdu" class="block text-gray-300 font-bold mb-2">Level of Education:</label>
                <select id="levelEdu" name="levelEdu" class="form-select rounded-md shadow-sm w-full bg-gray-700 text-gray-100" {{ Auth::user()->hasRole('admin') ? 'disabled' : '' }} required>
                    <option value="Diploma" {{ $program->levelEdu == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                    <option value="Undergraduate" {{ $program->levelEdu == 'Undergraduate' ? 'selected' : '' }}>Undergraduate</option>
                    <option value="Postgraduate" {{ $program->levelEdu == 'Postgraduate' ? 'selected' : '' }}>Postgraduate</option>
                </select>
                @if(Auth::user()->hasRole('admin'))
                    <input type="hidden" name="levelEdu" value="{{ $program->levelEdu }}">
                @endif
            </div>

            <div class="mb-4">
                <label for="NEC" class="block text-gray-300 font-bold mb-2">National Education Code:</label>
                <select id="NEC" name="NEC" class="form-select rounded-md shadow-sm w-full bg-gray-700 text-gray-100" {{ Auth::user()->hasRole('admin') ? 'disabled' : '' }} required>
                    <option value="Code1" {{ $program->NEC == 'Code1' ? 'selected' : '' }}>Code1</option>
                    <option value="Code2" {{ $program->NEC == 'Code2' ? 'selected' : '' }}>Code2</option>
                    <option value="Code3" {{ $program->NEC == 'Code3' ? 'selected' : '' }}>Code3</option>
                </select>
                @if(Auth::user()->hasRole('admin'))
                    <input type="hidden" name="NEC" value="{{ $program->NEC }}">
                @endif
            </div>

            <!-- Additional Boolean Attributes -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                @php
                    $attributes = [
                        'isInterviewExam' => 'Interview Exam',
                        'isUjianMedsi' => 'Ujian Medsi',
                        'isRayuan' => 'Rayuan',
                        'isDDegree' => 'DDegree',
                        'learnMod' => 'Learn Mode',
                        'isBumiputera' => 'Bumiputera',
                        'isTVET' => 'TVET',
                        'isKompetitif' => 'Kompetitif',
                        'isBTECH' => 'BTECH',
                        'isOKU' => 'OKU'
                    ];
                @endphp

                @foreach($attributes as $key => $label)
                <div class="mb-4">
                    <label for="{{ $key }}" class="block text-gray-300 font-bold mb-2">{{ $label }}:</label>
                    <select id="{{ $key }}" name="{{ $key }}" class="form-select rounded-md shadow-sm w-full bg-gray-700 text-gray-100" {{ Auth::user()->hasRole('admin') ? 'disabled' : '' }} required>
                        <option value="1" {{ $program->$key == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $program->$key == 0 ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                @endforeach
            </div>

        <!-- Program Fee and Description at the Bottom, Side by Side -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="programFee" class="block text-gray-300 font-bold mb-2">Program Fee:</label>
                <textarea id="programFee" name="programFee"
                    class="form-textarea rounded-md shadow-sm w-full bg-gray-700 text-gray-100"
                    rows="6"
                    style="min-height: 150px;"
                    {{ Auth::user()->hasRole('admin') ? 'disabled' : '' }}>{{ old('programFee', $program->programFee) }}</textarea>
                @if(Auth::user()->hasRole('admin'))
                    <input type="hidden" name="programFee" value="{{ $program->programFee }}">
                @endif
            </div>

            <div>
                <label for="programDesc" class="block text-gray-300 font-bold mb-2">Program Description:</label>
                <textarea id="programDesc" name="programDesc"
                    class="form-textarea rounded-md shadow-sm w-full bg-gray-700 text-gray-100"
                    rows="6"
                    style="min-height: 150px;"
                    {{ Auth::user()->hasRole('admin') ? 'disabled' : '' }}>{{ old('programDesc', $program->programDesc) }}</textarea>
                @if(Auth::user()->hasRole('admin'))
                    <input type="hidden" name="programDesc" value="{{ $program->programDesc }}">
                @endif
            </div>
        </div>


            <!-- Program Status (Admin only) -->
            @if(Auth::user()->hasRole('admin'))
            <div class="mb-4">
                <label for="programStatus" class="block text-gray-300 font-bold mb-2">Program Status:</label>
                <select name="programStatus" id="programStatus" class="form-select rounded-md shadow-sm w-full bg-gray-700 text-gray-100" required>
                    <option value="Pending" {{ $program->programStatus == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Approved" {{ $program->programStatus == 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Rejected" {{ $program->programStatus == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            @else
            <div class="mb-4">
                <label for="programStatus" class="block text-gray-300 font-bold mb-2">Program Status:</label>
                <p id="programStatus" class="bg-gray-700 text-gray-100 rounded-md shadow-sm p-2">{{ $program->programStatus }}</p>
            </div>
            @endif

            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Update Program
            </button>
        </form>
    </div>
</x-app-layout>
