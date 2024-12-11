<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Interview Schedule') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Calendar Section -->
            <div class="md:col-span-3 bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-semibold text-gray-100 mb-4">Interview Schedule Calendar</h3>
                <div id="calendar" class="text-gray-100 rounded-lg shadow-lg"></div>
            </div>

            <!-- Details Section -->
            <div id="details-card" class="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6 hidden">
                <h3 class="text-lg font-semibold text-gray-100 mb-4">Interview Details</h3>
                <div id="details-content" class="space-y-4 text-gray-300">
                    <!-- Details will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- FullCalendar JavaScript Setup -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const detailsCard = document.getElementById('details-card');
            const detailsContent = document.getElementById('details-content');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: '/interviews-schedule/calendar-events',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay',
                },
                height: 'auto',
                aspectRatio: 2, // Adjust aspect ratio for better spacing
                dateClick: function (info) {
                    document.querySelectorAll('.fc-day-selected').forEach((el) => {
                        el.classList.remove('fc-day-selected');
                    });

                    info.dayEl.classList.add('fc-day-selected');
                    fetchEventsForDate(info.dateStr);
                },
            });

            calendar.render();

            function fetchEventsForDate(date) {
    fetch(`/interviews-schedule/events-for-date?date=${date}`)
        .then((response) => response.json())
        .then((data) => {
            console.log(data); // Log to debug
            detailsContent.innerHTML = '';

            if (data.length > 0) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                data.forEach((event) => {
                    const eventCard = `
<div class="flex flex-col justify-between h-full p-4 bg-gray-700 rounded-lg shadow-lg mb-4 border border-gray-700">
    <div>
        <h4 class="text-lg font-semibold text-gray-100">${event.title}</h4>
        <p><strong>Time:</strong> ${event.scheduled_date}</p>
        <p><strong>Status:</strong> ${event.status}</p>
        <p><strong>Remarks:</strong> ${event.remarks || 'N/A'}</p>
    </div>
    <form method="POST" action="/send-email" class="mt-4">
        <input type="hidden" name="_token" value="${csrfToken}">
        <input type="hidden" name="interviewee_id" value="${event.interviewee_id}">
        <input type="hidden" name="scheduled_date" value="${event.scheduled_date}">
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            Send Email
        </button>
    </form>
</div>
                    `;
                    detailsContent.innerHTML += eventCard;
                });
                detailsCard.classList.remove('hidden');
            } else {
                detailsContent.innerHTML = `
                    <div class="p-4 bg-gray-700 rounded-lg shadow">
                        <p class="text-center text-gray-100">No interviews scheduled for this date.</p>
                    </div>
                `;
                detailsCard.classList.remove('hidden');
            }
        });
}


        });
    </script>

    <!-- Tailwind Styling for Better Visuals -->
    <style>
        .fc-daygrid-day.fc-day-selected {
            background-color: #2b6cb0; /* Highlight selected date */
            color: white;
        }

        .fc-toolbar-title {
            font-size: 1.5rem; /* Larger title for better hierarchy */
            font-weight: bold;
            color: #edf2f7; /* Lighter color */
        }

        .fc .fc-button-primary {
            background-color: #4a5568; /* Button background */
            border: none;
        }

        .fc .fc-button-primary:hover {
            background-color: #2d3748; /* Darker hover effect */
        }

        .fc-daygrid-day-number {
            font-size: 1rem;
            font-weight: bold;
            color: white; /* Light color for date numbers */
        }
        /* Style the day bar header row (Sun, Mon, Tue, etc.) */
        .fc .fc-col-header-cell {
            background-color: #1a202c; /* Dark background color for the header */
            border: 1px solid #2d3748; /* Border to match the theme */
            color: #cbd5e0; /* Light text color */
        }

        /* Style the text within the day header cells */
        .fc .fc-col-header-cell-cushion {
            color: #cbd5e0; /* Ensure the text color matches the theme */
            font-weight: bold; /* Optional: Make the text stand out */
            font-size: 1rem; /* Optional: Adjust the font size */
        }

        /* Adjust the borders of the calendar cells for consistency */
        .fc .fc-daygrid-day {
            border-color: #2d3748; /* Match the border color to the theme */
        }

        /* Button placement for Send Email */
        .relative .absolute {
            position: absolute;
        }
        .relative .bottom-4 {
            bottom: 1rem;
        }
        .relative .right-4 {
            right: 1rem;
        }

    </style>
</x-app-layout>
