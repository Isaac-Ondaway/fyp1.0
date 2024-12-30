<x-app-layout>
<script>
    window.PUSHER_APP_KEY = "{{ env('PUSHER_APP_KEY') }}";
    window.PUSHER_APP_CLUSTER = "{{ env('PUSHER_APP_CLUSTER') }}";
</script>
<script src="https://cdn.jsdelivr.net/npm/pusher-js@7.2.0/dist/web/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.1/dist/echo.iife.min.js"></script>

<script src="{{ asset('js/live-chat.js') }}"></script>

<x-slot name="header">
<div class="flex justify-between items-center">
        <h2 class="font-semibold text-2xl text-gray-100 leading-tight">
            {{ __('Dashboard') }}
        </h2>
        <a href="{{ route('reports.combined') }}" 
           class="inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            View Reports
        </a>
    </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Include Live Chat Modal -->
                        @if(auth()->user()->hasRole('faculty'))
                            @include('components.live-chat-modal') <!-- Floating Live Chat for Faculty -->
                        @elseif(auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.chats.index') }}" class="btn btn-primary floating-manage-chat">
                            Manage Chats
                        </a>
                        @endif

                    <!-- Show Synced Google Account -->
                    @if (Auth::user()->google_email)
                        <div class="mt-4 text-sm text-gray-600">
                            <strong>Google Account Synced:</strong> {{ Auth::user()->google_email }}
                        </div>
                    @endif

                        <!-- Calendar Component -->
                        <div class="mt-4" id="app">
                            <calendar-component></calendar-component>
                        </div>

                        
                </div>
            </div>
        </div>
    </div>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</x-app-layout>
<script>
    const CURRENT_USER_ID = {{ auth()->id() }};
    window.CURRENT_USER_ID = {{ auth()->id() }};
</script>

<style>
.floating-manage-chat {
    position: fixed;
    bottom: 20px; /* Distance from the bottom */
    right: 50px; /* Distance from the right */
    z-index: 1000; /* Ensure it stays above other elements */
    padding: 10px 15px; /* Optional: Adjust padding */
    border-radius: 50px; /* Optional: Makes the button rounded */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Optional: Adds a shadow */
    background-color:rgb(76, 102, 175); /* Change this to your desired color (e.g., green) */
    color: #ffffff; /* Ensure text color is readable */
    transition: all 0.3s ease; /* Smooth hover effect */
}

/* Hover Effect */
.floating-manage-chat:hover {
    transform: scale(1.1); /* Slightly enlarge on hover */
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15); /* Enhance shadow on hover */
    background-color:rgb(83, 69, 160); /* Slightly darker color on hover */
}

</style>


