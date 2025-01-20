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

    <!-- Interview Details Modal -->
    <div id="interviewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-gray-900 rounded-lg shadow-lg max-w-lg w-full p-8">
        <h3 class="text-2xl font-bold text-gray-200 mb-6 text-center border-b border-gray-700 pb-3">
            Interview Details
        </h3>
        <form id="updateInterviewForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="interview_id" id="modalInterviewID">

            <div class="text-gray-300 space-y-6">

                <p><strong>Program Name:</strong> <span id="modalProgramName">N/A</span></p> <!-- Program Name -->
                <p><strong>Batch:</strong> <span id="modalBatchName">N/A</span></p>
                <!-- Interviewee Name -->
                <div>
                    <label class="block font-semibold text-sm text-gray-400">Interviewee Name:</label>
                    <div class="text-lg text-gray-100 font-medium" id="modalIntervieweeName">N/A</div>
                </div>

                <!-- Contact Number -->
                <div>
                    <label class="block font-semibold text-sm text-gray-400">Contact Number:</label>
                    <div class="text-lg text-gray-100 font-medium" id="modalContactNumber">N/A</div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block font-semibold text-sm text-gray-400">Email:</label>
                    <div class="text-lg text-gray-100 font-medium" id="modalEmail">N/A</div>
                </div>

                <!-- Venue -->
                <div>
                    <label for="modalVenue" class="block font-semibold text-sm text-gray-400">Venue:</label>
                    <input type="text" id="modalVenue" name="venue" class="w-full rounded-md bg-gray-700 text-gray-100 p-3">
                </div>

                <!-- Status -->
                <div>
                    <label for="modalStatus" class="block font-semibold text-sm text-gray-400">Status:</label>
                    <select id="modalStatus" name="status" class="w-full rounded-md bg-gray-700 text-gray-100 p-2">
                        <option value="Pending">Pending</option>
                        <option value="Scheduled">Scheduled</option>
                        <option value="Attended">Attended</option>
                        <option value="Absent">Absent</option>
                        <option value="Accepted">Accepted</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>

                <!-- Remarks -->
                <div>
                    <label for="modalRemarks" class="block font-semibold text-sm text-gray-400">Remarks:</label>
                    <textarea id="modalRemarks" name="remarks" class="w-full rounded-md bg-gray-700 text-gray-100 p-3" rows="3" placeholder="Add your remarks here..."></textarea>
                </div>

            </div>
            

            <!-- Buttons -->
            <div class="mt-8 flex justify-end space-x-4">
                <button type="button" onclick="closeInterviewModal()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Close
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Save Changes
                </button>
            </div>
        </form>
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
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
                },
                height: 'auto',
                aspectRatio: 2,
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
                        detailsContent.innerHTML = ''; // Clear existing content

                        if (data.length > 0) {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                            data.forEach((event) => {
                                const eventCard = `
<div class="flex flex-col justify-between h-full p-4 bg-gray-700 rounded-lg shadow-lg mb-4 border border-gray-700" onclick='openInterviewModal(${JSON.stringify(event)})'>
    <div>
        <h4 class="text-lg font-semibold text-gray-100">${event.title}</h4>
        <p><strong>Status:</strong> ${event.status}</p>
    </div>
    <div class="flex space-x-2 mt-4" onclick="event.stopPropagation()">
        <form method="POST" action="/send-email">
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="interviewee_id" value="${event.interviewee_id}">
            <input type="hidden" name="scheduled_date" value="${event.scheduled_date}">
            <button type="submit" class="px-4 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                Send Email
            </button>
        </form>

        <form method="POST" action="/interviews-schedule/${event.schedule_id}">
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="px-4 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                Delete
            </button>
        </form>
    </div>
</div>
`;
                                detailsContent.innerHTML += eventCard;
                            });
                            detailsCard.classList.remove('hidden');
                        } else {
                            detailsContent.innerHTML = `
<div class="p-4 bg-gray-700 rounded-lg shadow">
    <p class="text-center text-gray-100">No interviews scheduled for this date.</p>
</div>`;
                            detailsCard.classList.remove('hidden');
                        }
                    })
                    .catch((error) => console.error('Error fetching events:', error));
            }
        });

        function openInterviewModal(schedule) {
            console.log(schedule);
        const form = document.getElementById('updateInterviewForm');
        form.action = `/interviews-schedule/${schedule.schedule_id}`;

        document.getElementById('modalInterviewID').value = schedule.interviewee_id;
        document.getElementById('modalIntervieweeName').innerText = schedule.title || 'N/A';
        document.getElementById('modalContactNumber').innerText = schedule.contactNumber || 'N/A';
        document.getElementById('modalEmail').innerText = schedule.email || 'N/A';
        document.getElementById('modalVenue').value = schedule.venue || '';
        document.getElementById('modalStatus').value = schedule.status || 'Pending';
        document.getElementById('modalRemarks').value = schedule.remarks || '';

            // Populate Program Name and Batch
    document.getElementById('modalProgramName').innerText = schedule.programName || 'N/A';
    document.getElementById('modalBatchName').innerText = schedule.batchName || 'N/A';

        document.getElementById('interviewModal').classList.remove('hidden');
    }

    function closeInterviewModal() {
        document.getElementById('interviewModal').classList.add('hidden');
    }

    </script>

    <!-- Tailwind Styling for Better Visuals -->
<style>
        
        #interviewModal {
        z-index: 1050; /* Set a high z-index for the modal */
    }
        .fc-daygrid-day.fc-day-selected {
            background-color: #2b6cb0;
            color: white;
        }

        .fc-toolbar-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #edf2f7;
        }

        .fc .fc-button-primary {
            background-color: #4a5568;
            border: none;
        }

        .fc .fc-button-primary:hover {
            background-color: #2d3748;
        }

        .fc-daygrid-day-number {
            font-size: 1rem;
            font-weight: bold;
            color: white;
        }

        .fc .fc-col-header-cell {
            background-color: #1a202c;
            border: 1px solid #2d3748;
            color: #cbd5e0;
        }

        .fc .fc-col-header-cell-cushion {
            color: #cbd5e0;
            font-weight: bold;
            font-size: 1rem;
        }

        .fc .fc-daygrid-day {
            border-color: #2d3748;
        }

        .fc-list-item-time {
            font-weight: bold;
            color: #ffffff; /* Ensure the date/time is visible */
        }
        .fc-list-item-title {
            color: #ffffff;
        }

        /* General list view event rows */
        .fc-list-event {
            background-color: #2d3748 !important; /* Dark gray background for event rows */
            color: #e2e8f0 !important; /* Light text for event rows */
            border-bottom: 1px solid #374151; /* Border between events */
        }

        /* Hover effect for entire event rows */
        .fc-list-event:hover {
            background-color: rgb(233, 100, 12) !important; /* Highlighted background on hover */
            color: rgb(14, 1, 1) !important; /* Default text color for row on hover */
        }

        /* Ensure title text changes to black on hover */
        .fc-list-event:hover .fc-list-item-title {
            color: rgb(14, 1, 1) !important; /* Black text for event title on hover */
        }

        /* Ensure time text retains styling */
        .fc-list-item-time {
            font-weight: bold;
            color: #63b3ed !important; /* Light blue for time */
        }

        /* Ensure title text retains styling */
        .fc-list-item-title {
            font-size: 1rem;
            color: #ffffff !important; /* White for event title */
        }

        /* Fix for date text inside the header */
        .fc-list-day-cushion {
            color: rgb(19, 1, 1) !important; /* Ensure date text in headers is dark */
        }

        /* Adjust event content alignment */
        .fc-list-event > td {
            vertical-align: middle;
        }

        /* Highlight fixes for list view */
        .fc-highlight {
            background-color: #374151 !important; /* Highlighted rows visible */
            color: #ffffff !important; /* Text remains visible */
        }

</style>
</x-app-layout>
