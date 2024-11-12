<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Create New Batch') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
            <h1 class="text-3xl font-bold text-white mb-4">Create New Batch</h1>

            <form action="{{ route('batches.store') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="batchID" class="block text-gray-300 font-semibold mb-2">Batch ID</label>
                    <input type="text" class="w-full border border-gray-600 bg-gray-900 text-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" id="batchID" name="batchID" required>
                </div>
                <div>
                    <label for="batchName" class="block text-gray-300 font-semibold mb-2">Batch Name</label>
                    <input type="text" class="w-full border border-gray-600 bg-gray-900 text-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" id="batchName" name="batchName" required>
                </div>
                <div>
                    <label for="batchStartDate" class="block text-gray-300 font-semibold mb-2">Batch Start Date</label>
                    <input type="date" class="w-full border border-gray-600 bg-gray-900 text-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" id="batchStartDate" name="batchStartDate" required>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded shadow-lg transition duration-200">
                    Create Batch
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
