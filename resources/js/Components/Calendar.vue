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
  <FullCalendar :key="calendarKey" ref="fullCalendar" :options="calendarOptions" />
</template>

<script>
import { nextTick, watch } from 'vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';

export default {
  components: {
    FullCalendar,
  },
  data() {
    return {
      calendarKey: 0, // Initial key
      calendarOptions: {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
        },
        editable: true,
        selectable: true,
        events: '/api/google-calendar-events', // Load events from your API directly
      },
    };
  },
  async mounted() {
    // Fetch events from Google Calendar
    try {
      const response = await fetch('/api/google-calendar-events');
      const events = await response.json();
      
      // Log events to verify that each event has an `id`
      console.log("Fetched events:", events);

      if (Array.isArray(events)) {
        // Map events to FullCalendar format
        this.calendarOptions.events = events.map(event => ({
          id: event.id, // Ensure `id` is defined
          title: event.summary,
          start: event.start,
          end: event.end,
          editable: true,
        }));
      } else {
        console.error('Unexpected response:', events);
      }
    } catch (error) {
      console.error('Error fetching events:', error);
    }
  },
  watch: {
    'calendarOptions.events': async function () {
      // Wait until the DOM is updated
      await nextTick();

      // Access FullCalendar instance and set event handlers
      const calendar = this.$refs.fullCalendar?.getApi();
      if (calendar) {
        calendar.setOption('select', this.handleDateSelect);
        calendar.setOption('eventClick', this.handleEventClick);
        calendar.setOption('eventDrop', this.handleEventDrop);
      }
    },
  },
  methods: {
    async refreshEvents() {
      try {
        const response = await fetch('/api/google-calendar-events');
        const events = await response.json();

        if (Array.isArray(events)) {
          this.calendarOptions.events = events.map(event => ({
            id: event.id,
            title: event.summary,
            start: event.start,
            end: event.end,
            editable: true,
          }));
          
          // Force calendar re-render
          this.calendarKey += 1;
        } else {
          console.error('Unexpected response format:', events);
        }
      } catch (error) {
        console.error('Error fetching events:', error);
      }
    },


    grantGoogleAccess() {
      // Redirect to the backend route for Google OAuth
      window.location.href = '/auth/google-calendar';
    },
    async handleDateSelect(info) {
      const title = prompt('Enter event title:');
      if (title) {
    const startDate = new Date(info.startStr);
    let endDate = new Date(info.endStr || info.startStr);

    // Set default start time at 9:00 AM if missing
    if (startDate.getHours() === 0 && startDate.getMinutes() === 0) {
      startDate.setHours(9, 0);
    }

    // Adjust the end date to avoid adding an extra day
    if (info.view.type === 'dayGridMonth' && startDate.toDateString() !== endDate.toDateString()) {
      // Set end date to one day before if in month view and it’s a multi-day range
      endDate.setDate(endDate.getDate() - 1);
      endDate.setHours(17, 0); // Set end time to 5:00 PM
    } else if (endDate.getHours() === 0 && endDate.getMinutes() === 0) {
      // Otherwise, set a default end time if only a single day is selected
      endDate.setHours(17, 0);
    }

    const event = {
      title,
      start: startDate.toISOString(),
      end: endDate.toISOString(),
    };
      // Save the event to Google Calendar through your Laravel API
      try {
          const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
          const response = await fetch('/api/google-calendar-events', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json',  
            'X-CSRF-TOKEN': csrfToken, // Include CSRF token
          },
            body: JSON.stringify(event),
          });

         const savedEvent = await response.json();
         console.log('Saved Event:', savedEvent);
         if (response.ok) {
            await this.refreshEvents();  // Refresh events to include the new event
          } else {
            console.error('Failed to save event to Google Calendar');
          }
        } catch (error) {
          console.error('Error saving event to Google Calendar:', error);
        } 
  }
},
    handleEventClick(info) {
      const event = info.event;
      const isDelete = confirm(`Event: ${event.title}\nStart: ${event.start}\nEnd: ${event.end}\n\nDo you want to delete this event?`);
      if (isDelete) {
            this.deleteEventInGoogle(event.id);
        }
    },
    async deleteEventInGoogle(eventId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    try {
        const response = await fetch(`/api/google-calendar-events/${eventId}`, {
            method: 'DELETE',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken 
            },
        });

        if (!response.ok) throw new Error('Failed to delete event');

        // Remove the event from FullCalendar's events array
        this.calendarOptions.events = this.calendarOptions.events.filter(e => e.id !== eventId);

        // Optional: Re-render the calendar to ensure the event is removed
        this.$refs.fullCalendar.getApi().render();

        alert('Event deleted successfully');
    } catch (error) {
        console.error('Error deleting event from Google:', error);
        alert('Failed to delete event');
    }
},

    async handleEventDrop(info) {
      const event = info.event;
      console.log(event.id)
      console.log("Event start time:", event.start); // Debugging
      console.log("Event end time:", event.end); // Debugging
      // Check if `id` is defined before making the request
      if (!event.id) {
        console.error('Event id is undefined. Cannot update event.');
        alert('Error: Event id is missing. Please try again.');
        info.revert();
        return;
      }

      if (confirm(`Save changes to event "${event.title}"?`)) {
        try {
          await this.updateEventInGoogle(event);
          alert('Event updated successfully');
        } catch (error) {
          console.error('Error updating event on Google:', error);
          alert('Failed to save event changes');
          info.revert(); // Revert on error
        }
      } else {
        info.revert(); // Revert if the user cancels
      }
    },
    async saveEventToGoogle(event) {
      try {
        const response = await fetch('/api/google-calendar-events', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            title: event.title,
            start: event.start,
            end: event.end,
          }),
        });
        if (!response.ok) throw new Error('Failed to create event');
      } catch (error) {
        console.error('Error saving event to Google:', error);
      }
    },
    async updateEventInGoogle(event) {
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  try {
    const response = await fetch(`/api/google-calendar-events/${event.id}`, {
         method: 'PATCH',  
         headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken // Add CSRF token here
         },
         body: JSON.stringify({
            start: event.start.toISOString(),
            end: event.end.toISOString(),
         }),
    });
    if (!response.ok) throw new Error('Failed to update event');

    const updatedEvent = await response.json();

    // Update the event directly in the calendar options
    const eventIndex = this.calendarOptions.events.findIndex(e => e.id === updatedEvent.event.id);
    if (eventIndex !== -1) {
      this.calendarOptions.events[eventIndex] = {
        id: updatedEvent.event.id,
        title: updatedEvent.event.summary,
        start: updatedEvent.event.start.dateTime || updatedEvent.event.start.date,
        end: updatedEvent.event.end.dateTime || updatedEvent.event.end.date,
      };
      this.$refs.fullCalendar.getApi().refetchEvents(); // Refresh calendar events
    }
  } catch (error) {
    console.error('Error updating event on Google:', error);
  }
},

}
};
</script>

<style scoped>
#calendar-container {
  max-width: 900px;
  margin: 0 auto;
}
</style>
