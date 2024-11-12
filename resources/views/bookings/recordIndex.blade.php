<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Bookings') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-extrabold mb-8 text-gray-200">Bookings List</h1>

        <!-- Booking Tabs -->
        <div class="mb-6">
            <div class="flex space-x-4 border-b border-gray-300">
                <button class="tab-button py-2 px-4 font-semibold text-blue-500 border-b-4 border-transparent hover:border-blue-200 focus:border-blue-500 active-tab" data-tab="upcoming">
                    Upcoming
                </button>
                <button class="tab-button py-2 px-4 font-semibold text-yellow-500 border-b-4 border-transparent hover:border-yellow-200 focus:border-yellow-500" data-tab="pending">
                    Pending
                </button>
                <button class="tab-button py-2 px-4 font-semibold text-green-500 border-b-4 border-transparent hover:border-green-200 focus:border-green-500" data-tab="confirmed">
                    Confirmed
                </button>
                <button class="tab-button py-2 px-4 font-semibold text-red-500 border-b-4 border-transparent hover:border-red-200 focus:border-red-500" data-tab="cancelled">
                    Cancelled
                </button>
            </div>
        </div>

        <!-- Tab Contents -->
        <div class="tab-content space-y-6" id="upcoming">
            <h2 class="text-xl font-semibold text-blue-600 mb-4">Upcoming Confirmed Bookings (Next Month)</h2>
            <div class="space-y-6">
                @foreach ($upcomingBookings as $booking)
                    @include('bookings.partials.booking-card', ['booking' => $booking])
                @endforeach
                @if($upcomingBookings->isEmpty())
                    <p class="text-gray-200">No upcoming confirmed bookings within the next month.</p>
                @endif
            </div>
        </div>

        <div class="tab-content space-y-6 hidden" id="pending">
            <h2 class="text-xl font-semibold text-yellow-600 mb-4">Pending Bookings</h2>
            <div class="space-y-6">
                @foreach ($pendingBookings as $booking)
                    @include('bookings.partials.booking-card', ['booking' => $booking])
                @endforeach
                @if($pendingBookings->isEmpty())
                    <p class="text-gray-200">No pending bookings found.</p>
                @endif
            </div>
        </div>

        <div class="tab-content space-y-6 hidden" id="confirmed">
            <h2 class="text-xl font-semibold text-green-600 mb-4">Confirmed Bookings</h2>
            <div class="space-y-6">
                @foreach ($confirmedBookings as $booking)
                    @include('bookings.partials.booking-card', ['booking' => $booking])
                @endforeach
                @if($confirmedBookings->isEmpty())
                    <p class="text-gray-200">No confirmed bookings found.</p>
                @endif
            </div>
        </div>

        <div class="tab-content space-y-6 hidden" id="cancelled">
            <h2 class="text-xl font-semibold text-red-600 mb-4">Cancelled Bookings</h2>
            <div class="space-y-6">
                @foreach ($cancelledBookings as $booking)
                    @include('bookings.partials.booking-card', ['booking' => $booking])
                @endforeach
                @if($cancelledBookings->isEmpty())
                    <p class="text-gray-200">No cancelled bookings found.</p>
                @endif
            </div>
        </div>

        <!-- Details Section -->
        <div class="w-1/3 bg-gray-800 text-gray-200 rounded-lg p-6 ml-4 hidden" id="details-section">
            <h2 class="text-xl font-semibold mb-4">Event Details</h2>
            <div id="event-details-content">
                <p>Select an event to view details.</p>
            </div>
        </div>
    </div>
    <!-- JavaScript for Tab Switching and Dropdown Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // Hide all tab contents
                    tabContents.forEach(content => content.classList.add('hidden'));

                    // Remove active class from all buttons
                    tabButtons.forEach(btn => btn.classList.remove('active-tab'));

                    // Show selected tab content and highlight the button
                    const target = this.getAttribute('data-tab');
                    document.getElementById(target).classList.remove('hidden');
                    this.classList.add('active-tab');
                });
            });

            // Set default active tab
            tabButtons[0].click();

            // Handle dropdown toggling for each booking
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function (event) {
                    event.stopPropagation(); // Prevent click from closing it immediately
                    const dropdownMenu = this.nextElementSibling;
                    dropdownMenu.classList.toggle('hidden');
                });
            });

            // Hide dropdowns when clicking outside
            document.addEventListener('click', function () {
                const allDropdowns = document.querySelectorAll('.dropdown-menu');
                allDropdowns.forEach(menu => menu.classList.add('hidden'));
            });
        });
    </script>

    <!-- Additional Styles -->
    <style>
        .tab-button.active-tab {
            border-color: currentColor; /* Matches the text color (blue, yellow, green, red) */
            background-color: rgba(0, 0, 0, 0.1); /* Slightly darker background for better focus */
        }
    </style>
</x-app-layout>
