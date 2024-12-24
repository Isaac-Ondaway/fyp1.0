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

            <!-- Program ID -->
            <div>
                <label for="program_id" class="block text-lg font-bold text-gray-200">Program ID</label>
                <input type="text" id="program_id" name="program_id" value="{{ $interview->programID }}" class="w-full mt-2 bg-gray-700 text-gray-300 rounded-md py-2 px-3" readonly>
            </div>

            <!-- Batch ID -->
            <div>
                <label for="batch_id" class="block text-lg font-bold text-gray-200">Batch ID</label>
                <input type="text" id="batch_display" value="{{ $interview->batch->batchName }}" class="w-full mt-2 bg-gray-700 text-gray-300 rounded-md py-2 px-3" readonly>
                <input type="hidden" id="batch_id" name="batch_id" value="{{ $interview->batch->batchID }}">
            </div>

            <!-- Date Picker -->
            <div>
                <label for="scheduled_date" class="block text-lg font-bold text-gray-200">Date & Time Slot</label>
                <input type="text" id="scheduled_date" name="scheduled_date" class="w-full mt-2 bg-gray-700 text-gray-300 rounded-md py-2 px-3" placeholder="Select Date & Time">
            </div>

            <!-- Venue -->
            <div>
                <label for="venue" class="block text-lg font-bold text-gray-200">Venue</label>
                <textarea id="venue" name="venue" rows="3" class="w-full mt-2 bg-gray-700 text-gray-300 rounded-md py-2 px-3" placeholder="Enter the venue here..."></textarea>
            </div>

            <!-- Remarks -->
            <div>
                <label for="remarks" class="block text-lg font-bold text-gray-200">Remarks</label>
                <textarea id="remarks" name="remarks" rows="3" class="w-full mt-2 bg-gray-700 text-gray-300 rounded-md py-2 px-3" placeholder="Add any additional remarks here..."></textarea>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-lg font-bold text-gray-200">Status</label>
                <select id="status" name="status" class="w-full mt-2 bg-gray-700 text-gray-300 rounded-md py-2 px-3">
                    <option value="Pending" selected>Pending</option>
                    <option value="Scheduled">Scheduled</option>
                    <option value="Attended">Attended</option>
                    <option value="Absent">Absent</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-sm py-3 px-6 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-md transition duration-300 ease-in-out">
                    Schedule Interview
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        flatpickr("#scheduled_date", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: false,
            minDate: "today",
        });
    });
</script>

<style>
    label {
        font-size: 1.125rem; /* Larger font size */
    }

    input, textarea, select {
        font-size: 1rem; /* Ensure consistency */
    }

    button {
        font-size: 1.125rem; /* Make the button font size larger for better visibility */
    }
</style>
