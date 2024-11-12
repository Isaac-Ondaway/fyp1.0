<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Create New Booking') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <div class="bg-gray-900 p-6 rounded-lg shadow-lg">
            <h1 class="text-2xl font-extrabold mb-6 text-gray-200">New Booking</h1>

            <!-- Form for creating a new booking -->
            <form action="{{ route('bookings.store') }}" method="POST">
                @csrf

                <!-- Resource Selection -->
                <div class="mb-4">
                    <label for="resourceID" class="block text-gray-300 font-semibold mb-2">Select Resource:</label>
                    <select name="resourceID" id="resourceID" class="form-select w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">-- Select Resource --</option>
                        @foreach($resources as $resource)
                            <option value="{{ $resource->resourceID }}">{{ $resource->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Program Name -->
                <div class="mb-4">
                    <label for="programName" class="block text-gray-300 font-semibold mb-2">Program Name:</label>
                    <input type="text" name="programName" id="programName" class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <!-- Phone Number -->
                <div class="mb-4">
                    <label for="phoneNo" class="block text-gray-300 font-semibold mb-2">Phone Number:</label>
                    <input type="text" name="phoneNo" id="phoneNo" class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <!-- Number of Participants -->
                <div class="mb-4">
                    <label for="numberOfParticipant" class="block text-gray-300 font-semibold mb-2">Number of Participants:</label>
                    <input type="number" name="numberOfParticipant" id="numberOfParticipant" class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <!-- Matric Number -->
                <div class="mb-4">
                    <label for="matricNo" class="block text-gray-300 font-semibold mb-2">Matric Number:</label>
                    <input type="text" name="matricNo" id="matricNo" class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <!-- Start Time -->
                <div class="mb-4">
                    <label for="start_time" class="block text-gray-300 font-semibold mb-2">Start Time:</label>
                    <input type="datetime-local" name="start_time" id="start_time" class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <!-- End Time -->
                <div class="mb-4">
                    <label for="end_time" class="block text-gray-300 font-semibold mb-2">End Time:</label>
                    <input type="datetime-local" name="end_time" id="end_time" class="form-input w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <!-- Status Selection -->
                <div class="mb-4">
                    <label for="status" class="block text-gray-300 font-semibold mb-2">Status:</label>
                    <select name="status" id="status" class="form-select w-full rounded-md shadow-sm bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                        <option value="Pending">Pending</option>
                        <option value="Confirmed">Confirmed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="mt-6">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg">
                        Create Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
