<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Upload CSV File') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-extrabold mb-8 text-gray-200">Upload Interviewees CSV</h1>

        <!-- Upload CSV Form -->
        <form action="{{ route('interviews.bulkUpload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Upload CSV File Section -->
            <div>
                <label for="csv_file" class="block text-gray-300 font-semibold mb-2">Upload CSV File:</label>
                <input type="file" name="file" id="csv_file" class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
            </div>

            <!-- Spacer -->
            <div class="mt-4"></div>
            
            <!-- Upload Button -->
            <div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg">
                    Upload CSV
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
