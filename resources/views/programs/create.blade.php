<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 dark:text-gray-100 leading-tight">
            {{ __('Create New Program') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6 text-gray-100 dark:text-gray-100">Create New Program</h1>
        <form action="{{ route('programs.store') }}" method="POST" autocomplete="off">
            @csrf
            
            <!-- Program ID -->
            <div class="mb-4">
                <label for="programID" class="block text-gray-300 dark:text-gray-300 font-bold mb-2">Program ID:</label>
                <input type="text" name="programID" class="form-input rounded-md shadow-sm w-full bg-gray-800 text-gray-100" required>
            </div>

            <!-- Batch ID -->
            <div class="mb-4">
                <label for="batchID" class="block text-gray-300 dark:text-gray-300 font-bold mb-2">Batch:</label>
                <select name="batchID" class="form-select rounded-md shadow-sm w-full bg-gray-800 text-gray-100" required>
                    @foreach ($batches as $batch)
                        <option value="{{ $batch->batchID }}">{{ $batch->batchName }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Faculty Name and ID -->
            <div class="mb-4">
                <label for="facultyName" class="block text-gray-300 font-bold mb-2">Faculty Name:</label>
                <input type="text" name="facultyName" class="form-input rounded-md shadow-sm w-full bg-gray-800 text-gray-100" value="{{ $facultyName }}" disabled readonly>
            </div>
            <div class="mb-4">
                <input type="hidden" name="facultyID" value="{{ Auth::id() }}">
            </div>

            <!-- Program Name -->
            <div class="mb-4">
                <label for="programName" class="block text-gray-300 dark:text-gray-300 font-bold mb-2">Program Name:</label>
                <input type="text" name="programName" class="form-input rounded-md shadow-sm w-full bg-gray-800 text-gray-100" required>
            </div>

            <!-- Total Semesters -->
            <div class="mb-4">
                <label for="programSem" class="block text-gray-300 dark:text-gray-300 font-bold mb-2">Total Semesters:</label>
                <input type="number" name="programSem" class="form-input rounded-md shadow-sm w-full bg-gray-800 text-gray-100" required>
            </div>

            <!-- Level of Education -->
            <div class="mb-4">
                <label for="levelEdu" class="block text-gray-300 dark:text-gray-300 font-bold mb-2">Level of Education:</label>
                <select name="levelEdu" class="form-select rounded-md shadow-sm w-full bg-gray-800 text-gray-100" required>
                    <option value="Diploma">Diploma</option>
                    <option value="Undergraduate">Undergraduate</option>
                    <option value="Postgraduate">Postgraduate</option>
                </select>
            </div>

            <!-- National Education Code -->
            <div class="mb-4">
                <label for="NEC" class="block text-gray-300 dark:text-gray-300 font-bold mb-2">National Education Code:</label>
                <select name="NEC" class="form-select rounded-md shadow-sm w-full bg-gray-800 text-gray-100" required>
                    <option value="Code1">Code1</option>
                    <option value="Code2">Code2</option>
                    <option value="Code3">Code3</option>
                </select>
            </div>

            <!-- Program Fee -->
            <div class="mb-4">
                <label for="programFee" class="block text-gray-300 dark:text-gray-300 font-bold mb-2">Program Fee:</label>
                <input type="number" name="programFee" class="form-input rounded-md shadow-sm w-full bg-gray-800 text-gray-100" required>
            </div>

            <!-- Program Status -->
            <div class="mb-4">
                <label for="programStatus" class="block text-gray-300 font-bold mb-2">Program Status:</label>
                <input type="text" name="programStatus" class="form-input rounded-md shadow-sm w-full bg-gray-800 text-gray-100" value="Pending" readonly disabled>
            </div>

            <!-- Program Description -->
            <div class="mb-4">
                <label for="programDesc" class="block text-gray-300 dark:text-gray-300 font-bold mb-2">Program Description:</label>
                <textarea name="programDesc" class="form-textarea rounded-md shadow-sm w-full bg-gray-800 text-gray-100" rows="4"></textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create Program
            </button>
        </form>
    </div>
</x-app-layout>
