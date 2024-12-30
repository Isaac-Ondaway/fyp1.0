<template>
  <!-- Grant Google Access Button -->
  <div>
    <button
      class="btn btn-primary bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
      @click="grantGoogleAccess"
    >
      Grant Google Calendar Access
    </button>
  </div>

  <!-- FullCalendar Component -->
  <FullCalendar :key="calendarKey" ref="fullCalendar" :options="calendarOptions" />

<!-- Vue-controlled Modal -->
<div
  v-if="isModalOpen"
  class="fixed inset-0 flex items-center justify-center bg-grey bg-opacity-60 z-50"
>
  <!-- Modal Content -->
  <div
    class="bg-white rounded-lg shadow-2xl max-w-lg mx-auto p-6"
    style="border: 1px solid #e2e8f0;"
  >
    <!-- Modal Header -->
    <div class="flex justify-between items-center border-b pb-4 mb-4">
      <h2 class="text-2xl font-bold text-gray-800">Add New Event</h2>
      <button
        @click="closeModal"
        class="text-gray-600 hover:text-gray-900 text-3xl font-bold"
        aria-label="Close"
      >
        &times;
      </button>
    </div>

    <!-- Modal Body -->
    <form @submit.prevent="saveEvent">
      <div class="space-y-4">
        <!-- Title -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Title</label>
          <input
            type="text"
            v-model="eventForm.title"
            class="w-full border-gray-300 rounded-md shadow-sm p-2 text-gray-800 focus:ring focus:ring-blue-200"
            placeholder="Enter event title"
            required
          />
        </div>

        <!-- Description -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Description</label>
          <textarea
            v-model="eventForm.description"
            class="w-full border-gray-300 rounded-md shadow-sm p-2 text-gray-800 focus:ring focus:ring-blue-200"
            rows="3"
            placeholder="Enter event description"
          ></textarea>
        </div>

        <!-- Start Date -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Start Date</label>
          <input
            type="datetime-local"
            v-model="eventForm.start_datetime"
            class="w-full border-gray-300 rounded-md shadow-sm p-2 text-gray-800 focus:ring focus:ring-blue-200"
            required
          />
        </div>

        <!-- End Date -->
        <div>
          <label class="block text-sm font-medium text-gray-700">End Date</label>
          <input
            type="datetime-local"
            v-model="eventForm.end_datetime"
            class="w-full border-gray-300 rounded-md shadow-sm p-2 text-gray-800 focus:ring focus:ring-blue-200"
            required
          />
        </div>

        <!-- Event Color -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Event Color</label>
          <input
            type="color"
            v-model="eventForm.color"
            class="w-16 h-10 border-gray-300 rounded-md"
          />
        </div>

        <!-- Visibility -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Event Visibility</label>
            <div class="mt-2 flex items-center">
                <label class="inline-flex items-center">
                    <input type="radio" v-model="eventForm.visibility" value="public" class="form-radio text-blue-500" />
                    <span class="ml-2 text-gray-800">Public</span>
                </label>
                <label class="inline-flex items-center ml-6 p-2">
                    <input type="radio" v-model="eventForm.visibility" value="private" class="form-radio text-blue-500" />
                    <span class="ml-2 text-gray-800">Private</span>
                </label>
            </div>
        </div>


        <!-- All Day Checkbox -->
        <div class="flex items-center">
          <input
            type="checkbox"
            v-model="eventForm.all_day"
            class="rounded border-gray-300 focus:ring focus:ring-blue-200"
            id="allDayEvent"
          />
          <label for="allDayEvent" class="ml-2 text-sm text-gray-700">All Day Event</label>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="flex justify-end mt-6 border-t pt-4">
        <button
          type="button"
          @click="closeModal"
          class="px-4 py-2 text-gray-700 bg-gray-300 rounded hover:bg-gray-400"
        >
          Cancel
        </button>
        <button
          type="submit"
          class="ml-2 px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600"
        >
          Save Event
        </button>
      </div>
    </form>
  </div>
</div>
</template>

<script>
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';

export default {
  components: { FullCalendar },
  data() {
    return {
      calendarKey: 0, // Key for re-rendering calendar
      isModalOpen: false, // Controls modal visibility
      calendarOptions: {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
        },
        selectable: true,
        editable: true,
        select: this.handleDateSelect, // Open modal when a date is selected
        eventClick: this.handleEventClick, // Handle event clicks
        events: [], // Fetched events
      },
      eventForm: {
        title: '',
        description: '',
        start_datetime: '',
        end_datetime: '',
        color: '#3788d8',
        visibility: '',
        all_day: false,
      },
    };
  },
  mounted() {
    this.refreshEvents();
  },
  methods: {
    /**
     * Fetch and merge events from local database and Google Calendar
     */
    async refreshEvents() {
      try {
        const [localResponse, googleResponse] = await Promise.allSettled([
          fetch('/events/fetch'),
          fetch('/api/google-calendar-events'),
        ]);

        let localEvents = [];
        let googleEvents = [];

        if (localResponse.status === 'fulfilled') {
          const data = await localResponse.value.json();
          console.log("Local Events Data:", data);
          localEvents = data.map(event => ({
            id: `local-${event.id}`,
            title: event.title,
            start: event.start,
            end: event.end,
            color: event.color || '#3788d8',
          }));
        }

        if (googleResponse.status === 'fulfilled') {
          const googleData = await googleResponse.value.json();
          console.log("Google Events Data:", googleData);
      if (Array.isArray(googleData)) {
        googleEvents = googleData
  .filter(event => event.start && event.end) // Ensure both start and end exist
  .map(event => ({
    id: `google-${event.id}`,
    title: event.summary || 'No Title',
    start: event.start, // Use the direct start field
    end: event.end,     // Use the direct end field
    color: '#f39c12',
  }));

        console.log('Mapped Google Events:', googleEvents);

      } else {
        console.warn('Google events data is not an array or token missing');
      }
    } else {
      console.warn('Skipping Google events due to error:', googleResponse.reason);
    }

    // Merge Local Events Only for now
    this.calendarOptions.events = [...localEvents, ...googleEvents];
    console.log('Final Events:', [...localEvents, ...googleEvents]);

    this.calendarKey += 1; // Force re-render
  } catch (error) {
    console.error('Unexpected error while fetching events:', error);
  }
},

    /**
     * Handle date selection and open modal
     */
    handleDateSelect(info) {

      const formatDateTimeLocal = (date) => {
    // Convert Date object to YYYY-MM-DDTHH:MM format
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
  };

      this.eventForm = {
        title: '',
        description: '',
        start_datetime: formatDateTimeLocal(info.start),
        end_datetime: formatDateTimeLocal(info.end),

        color: '#3788d8',
        visibility: 'public',
        all_day: false,
      };

      // Open the modal
      this.isModalOpen = true;
    },

    /**
     * Close modal
     */
    closeModal() {
      this.isModalOpen = false;
    },

    /**
     * Save event
     */
    async saveEvent() {
      try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const response = await fetch('/events/store', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
          },
          body: JSON.stringify(this.eventForm),
        });

        if (response.ok) {
          await this.refreshEvents();
          this.isModalOpen = false; // Close modal after saving
          alert('Event saved successfully!');
        } else {
          console.error('Failed to save event');
        }
      } catch (error) {
        console.error('Error saving event:', error);
      }
    },

    /**
     * Handle event click (for deleting an event)
     */
    async handleEventClick(info) {
      if (confirm(`Delete event: ${info.event.title}?`)) {
        const isLocalEvent = info.event.id.startsWith('local-');
        const eventId = info.event.id.replace(/^(local-|google-)/, '');

        try {
          const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
          const url = isLocalEvent
            ? `/events/delete/${eventId}`
            : `/api/google-calendar-events/${eventId}`;

          const response = await fetch(url, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken },
          });

          if (response.ok) {
            await this.refreshEvents();
            alert('Event deleted successfully!');
          } else {
            console.error('Failed to delete event.');
          }
        } catch (error) {
          console.error('Error deleting event:', error);
        }
      }
    },

    grantGoogleAccess() {
      window.location.href = '/auth/google-calendar';
    },
  },
};
</script>

<style scoped>
#calendar-container {
  max-width: 900px;
  margin: 0 auto;
}
</style>

<style>
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