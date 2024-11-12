<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Bookings') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <!-- Header with Button Container -->
        <div class="w-1/3 pr-5 flex items-center justify-between mb-6">
            <!-- Booking Header -->
            <h1 class="text-3xl font-extrabold text-gray-200">Bookings</h1>
            
            <!-- Add Booking Button -->
            <a href="{{ route('bookings.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                + Add Booking
            </a>
        </div>

        <!-- Container for Tabs and Bookings -->
        <div class="flex">
            <!-- Left Side: Tabs and Bookings List -->
            <div class="w-1/3 pr-4">
                <!-- Booking Tabs -->
                <div class="flex space-x-4 mb-4">
                    <button class="tab-button py-2 px-4 font-semibold text-blue-500 border-b-4 border-transparent hover:border-blue-500" data-tab="upcoming">
                        Upcoming
                    </button>
                    <button class="tab-button py-2 px-4 font-semibold text-yellow-500 border-b-4 border-transparent hover:border-yellow-500" data-tab="pending">
                        Pending
                    </button>
                    <button class="tab-button py-2 px-4 font-semibold text-green-500 border-b-4 border-transparent hover:border-green-500" data-tab="confirmed">
                        Confirmed
                    </button>
                    <button class="tab-button py-2 px-4 font-semibold text-red-500 border-b-4 border-transparent hover:border-red-500" data-tab="cancelled">
                        Cancelled
                    </button>
                </div>

                <!-- Booking Cards -->
                <div class="space-y-3" id="tab-content">
                    <!-- Upcoming Tab Content -->
                    <div class="tab-content space-y-3" id="upcoming">
                        @foreach ($upcomingBookings as $booking)
                            @include('bookings.partials.booking-card', ['booking' => $booking])
                        @endforeach
                        @if($upcomingBookings->isEmpty())
                            <p class="text-gray-200">No upcoming confirmed bookings within the next month.</p>
                        @endif
                    </div>

                    <!-- Pending Tab Content -->
                    <div class="tab-content space-y-3 hidden" id="pending">
                        @foreach ($pendingBookings as $booking)
                            @include('bookings.partials.booking-card', ['booking' => $booking])
                        @endforeach
                        @if($pendingBookings->isEmpty())
                            <p class="text-gray-200">No pending bookings found.</p>
                        @endif
                    </div>

                    <!-- Confirmed Tab Content -->
                    <div class="tab-content space-y-3 hidden" id="confirmed">
                        @foreach ($confirmedBookings as $booking)
                            @include('bookings.partials.booking-card', ['booking' => $booking])
                        @endforeach
                        @if($confirmedBookings->isEmpty())
                            <p class="text-gray-200">No confirmed bookings found.</p>
                        @endif
                    </div>

                    <!-- Cancelled Tab Content -->
                    <div class="tab-content space-y-3 hidden" id="cancelled">
                        @foreach ($cancelledBookings as $booking)
                            @include('bookings.partials.booking-card', ['booking' => $booking])
                        @endforeach
                        @if($cancelledBookings->isEmpty())
                            <p class="text-gray-200">No cancelled bookings found.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Side: Event Details -->
            <div class="w-2/3 bg-gray-800 text-gray-200 rounded-lg p-6 ml-4" id="details-section">
                <h2 class="text-xl font-semibold mb-4">Event Details</h2>
                <div id="event-details-content">
                    <p>Select an event to view details.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Tab Switching and Event Details -->
    <script>
        console.log("Authenticated User:", @json(auth()->user()));
        document.addEventListener('DOMContentLoaded', function () {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            const eventCards = document.querySelectorAll('.event-card');
            const detailsSection = document.getElementById('details-section');
            const detailsContent = document.getElementById('event-details-content');
            const isAdmin = @json(auth()->user()->role === 'admin');

            tabButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // Hide all tab contents
                    tabContents.forEach(content => content.classList.add('hidden'));

                    // Remove active styles from all buttons
                    tabButtons.forEach(btn => {
                        btn.style.borderColor = "transparent"; 
                    });

                    // Show selected tab content and highlight the button
                    const target = this.getAttribute('data-tab');
                    document.getElementById(target).classList.remove('hidden');
                    
                    // Set active styles for the selected tab
                    if (target === "upcoming") {
                        this.style.borderColor = "blue"; // Blue color for Upcoming
                    } else if (target === "pending") {
                        this.style.borderColor = "yellow"; // Yellow color for Pending
                    } else if (target === "confirmed") {
                        this.style.borderColor = "green"; // Green color for Confirmed
                    } else if (target === "cancelled") {
                        this.style.borderColor = "red"; // Red color for Cancelled
                    }
                });
            });

            // Set default active tab
            tabButtons[0].click();

            // Handle Event Card Clicks
            eventCards.forEach(card => {
                card.addEventListener('click', function () {
                    const details = JSON.parse(this.getAttribute('data-details'));

                    // Display event details on the right
                    detailsContent.innerHTML = `
                        <h3 class="text-lg font-bold">${details.resource.name}</h3>
                        <p>${new Date(details.start_time).toLocaleString('en-GB')} - ${new Date(details.end_time).toLocaleString('en-GB')}</p>
                        <p class="mt-4">Booked by: ${details.student ? details.student.name : 'N/A'}</p>
                        <p>Matric No: ${details.matricNo ?? 'N/A'}</p>
                        <p>Phone No: ${details.phoneNo ?? 'N/A'}</p>
                        <p class="mt-4">Program Name: ${details.programName ?? 'N/A'}</p>
                        <p>Number of Participants: ${details.numberOfParticipant ?? 'N/A'}</p>
                        <p>Status: ${details.status}</p>
                        <p class="mt-4">Description: ${details.description || 'No additional information available.'}</p>
                        ${isAdmin ? `<a href="/bookings/${details.id}/edit" class="mt-4 inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg">Edit</a>` : ''}
                    `;
                    detailsSection.classList.remove('hidden');
                });
            });
        });
    </script>
</x-app-layout>
