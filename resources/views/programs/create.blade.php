<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Create New Program') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
            <h1 class="text-3xl font-bold text-white mb-4">Create New Program</h1>

            <form action="{{ route('programs.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Program Code -->
                <div>
                    <label for="programID" class="block text-gray-300 font-semibold mb-2">Program Code</label>
                    <input type="text" id="programID" name="programID" class="w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-2" required>
                </div>

                <!-- Batch -->
                <div>
                    <label for="batchID" class="block text-gray-300 font-semibold mb-2">Batch</label>
                    <select id="batchID" name="batchID" class="w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-2" required>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch->batchID }}">{{ $batch->batchName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Faculty (only for admin) -->
                @if (Auth::user()->hasRole('admin'))
                    <div>
                        <label for="facultyID" class="block text-gray-300 font-semibold mb-2">Faculty</label>
                        <select id="facultyID" name="facultyID" class="w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-2">
                            @foreach ($faculties as $faculty)
                                <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Program Name -->
                <div>
                    <label for="programName" class="block text-gray-300 font-semibold mb-2">Program Name</label>
                    <input type="text" id="programName" name="programName" class="w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-2" required>
                </div>

                <!-- Program Semester -->
                <div>
                    <label for="programSem" class="block text-gray-300 font-semibold mb-2">Number of Semesters</label>
                    <input type="number" id="programSem" name="programSem" class="w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-2" required>
                </div>

                <!-- Level of Education -->
                <div>
                    <label for="levelEdu" class="block text-gray-300 font-semibold mb-2">Level of Education</label>
                    <select id="levelEdu" name="levelEdu" class="w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-2" required>
                        <option value="Diploma">Diploma</option>
                        <option value="Undergraduate">Undergraduate</option>
                        <option value="Postgraduate">Postgraduate</option>
                    </select>
                </div>

                <!-- NEC -->
                <div>
                    <label for="NEC" class="block text-gray-300 font-semibold mb-2">NEC</label>
                    <select id="NEC" name="NEC" class="w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-2" required>
                        <option value="Code1">Code1</option>
                        <option value="Code2">Code2</option>
                        <option value="Code3">Code3</option>
                    </select>
                </div>

                <!-- Program Fee -->
                <div>
                    <label for="programFee" class="block text-gray-300 font-semibold mb-2">Program Fee</label>
                    <input type="text" id="programFee" name="programFee" class="w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-2" required>
                </div>

                <!-- Program Description -->
                <div>
                    <label for="programDesc" class="block text-gray-300 font-semibold mb-2">Program Description</label>
                    <textarea id="programDesc" name="programDesc" class="w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-2"></textarea>
                </div>

                <!-- Study Program -->
                <div>
                    <label for="studyProgram" class="block text-gray-300 font-semibold mb-2">Study Program</label>
                    <input type="text" id="studyProgram" name="studyProgram" class="w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-2" required>
                </div>

                <!-- Boolean Options -->
                @foreach (['isInterviewExam', 'isUjianMedsi', 'isRayuan', 'isDDegree', 'learnMod', 'isBumiputera', 'isTVET', 'isKompetitif', 'isBTECH', 'isOKU'] as $field)
                    <div>
                        <label for="{{ $field }}" class="block text-gray-300 font-semibold mb-2">{{ ucwords(str_replace('is', '', str_replace('_', ' ', $field))) }}</label>
                        <select id="{{ $field }}" name="{{ $field }}" class="w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-2" required>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                @endforeach

                <!-- Submit Button -->
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded shadow-lg transition duration-200">
                    Create Program
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
