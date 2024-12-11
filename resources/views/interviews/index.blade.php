<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Interviews') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <h3 class="text-xl font-semibold text-gray-100 mb-6">Interviewee List</h3>

        <!-- Filter Section -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <!-- Filter by Batch -->
                <div>
                    <label for="batchFilter" class="block text-gray-300 font-semibold mb-2">Filter by Batch:</label>
                    <select id="batchFilter" name="batchID" class="form-select w-full rounded-md bg-gray-700 text-gray-100 px-4 py-2">
                        <option value="">All Batches</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->batchID }}">{{ $batch->batchName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Add Interviewee Button -->
                <div class="ml-auto">
                    <a href="{{ route('interviews.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg">
                        Add Interviewee
                    </a>
                </div>
            </div>
        </div>

        <!-- Interview List Container -->
        <div id="interview-list">
            <!-- This is where the interview list will be dynamically loaded -->
            <p class="text-gray-200 text-lg mt-8">Please select a batch to view the interviewees.</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const batchFilter = document.getElementById('batchFilter');
            const interviewList = document.getElementById('interview-list');

            function fetchInterviews() {
                const batchID = batchFilter.value;

                // Perform AJAX request to fetch filtered interviews
                fetch(`{{ route('interviews.index') }}?batchID=${batchID}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(data => {
                    // Update the interview list container with the fetched HTML
                    interviewList.innerHTML = data;
                })
                .catch(error => {
                    console.error('Error fetching interview data:', error);
                });
            }

            // Fetch interviews when the batch filter is changed
            batchFilter.addEventListener('change', fetchInterviews);
        });
    </script>
</x-app-layout>
