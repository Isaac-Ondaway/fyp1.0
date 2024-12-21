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
            View Combined Report
        </a>
    </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}

                        <!-- Include Live Chat Modal -->
                        @if(auth()->user()->hasRole('faculty'))
                            @include('components.live-chat-modal') <!-- Floating Live Chat for Faculty -->
                        @elseif(auth()->user()->hasRole('admin'))
                            <a href="{{ route('admin.chats.index') }}" class="btn btn-primary">
                                Manage Chats
                            </a>
                        @endif


                        <div class="mt-4">
                            <a href="{{ route('programs.create') }}" class="btn btn-primary bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add New Program
                            </a>
                        </div>
                    <!-- Show Synced Google Account -->
                    @if (Auth::user()->google_email)
                        <div class="mt-4 text-sm text-gray-600">
                            <strong>Google Account Synced:</strong> {{ Auth::user()->google_email }}
                        </div>
                    @else
                        <div class="mt-4 text-sm text-gray-600">
                            <strong>Google Account Synced:</strong> None
                        </div>
                    @endif

                        <!-- Calendar Component -->
                        <div id="app">
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



