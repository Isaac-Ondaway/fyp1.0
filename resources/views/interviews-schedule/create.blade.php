<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Schedule Interview for ' . $interview->intervieweeName) }}
        </h2>
    </x-slot>

    <!-- Include Flatpickr CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <div class="container mx-auto py-8">
        <form action="{{ route('interviews-schedule.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="interviewee_id" value="{{ $interview->interviewID }}">

            <!-- Program ID (read-only input) -->
            <div>
                <label for="program_id" class="block text-sm font-medium text-gray-300">Program ID</label>
                <input type="text" id="program_id" name="program_id" value="{{ $interview->programID }}" class="w-full mt-1 bg-gray-700 text-gray-300 rounded-md" readonly>
            </div>

            <!-- Batch ID (read-only input) -->
            <div>
                <label for="batch_id" class="block text-sm font-medium text-gray-300">Batch ID</label>
                <!-- Display the batch name in a readonly input -->
                <input type="text" id="batch_display" value="{{ $interview->batch->batchName }}" class="w-full mt-1 bg-gray-700 text-gray-300 rounded-md" readonly>
                
                <!-- Hidden input to store and submit the batch ID -->
                <input type="hidden" id="batch_id" name="batch_id" value="{{ $interview->batch->batchID }}">
            </div>

            <!-- Date Picker -->
            <div>
                <label for="scheduled_date" class="block text-sm font-medium text-gray-300">Date & Time Slot</label>
                <input type="text" id="scheduled_date" name="scheduled_date" class="w-full mt-1 bg-gray-700 text-gray-300 rounded-md" placeholder="Select Date & Time">
            </div>

            <!-- Remarks -->
            <div>
                <label for="remarks" class="block text-sm font-medium text-gray-300">Remarks</label>
                <textarea id="remarks" name="remarks" rows="3" class="w-full mt-1 bg-gray-700 text-gray-300 rounded-md"></textarea>
            </div>

            <!-- Status (default to "Pending") -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-300">Status</label>
                <select id="status" name="status" class="w-full mt-1 bg-gray-700 text-gray-300 rounded-md" required>
                    <option value="Pending" selected>Pending</option>
                    <option value="Scheduled">Scheduled</option>
                    <option value="Attended">Attended</option>
                    <option value="Absent">Absent</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-md">Schedule Interview</button>
            </div>
        </form>
    </div>
</x-app-layout>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        flatpickr("#scheduled_date", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: false, // Set to true for 24-hour format
            minDate: "today",
        });
    });
</script>