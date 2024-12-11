<!-- resources/views/admin/chats/index.blade.php -->

<x-app-layout>

<script>
        window.PUSHER_APP_KEY = "{{ env('PUSHER_APP_KEY') }}";
        window.PUSHER_APP_CLUSTER = "{{ env('PUSHER_APP_CLUSTER') }}";
        window.CURRENT_USER_ID = {{ auth()->id() ?? 'null' }};
    </script>
    <script>
    const CURRENT_USER_ID = {{ auth()->id() }};
</script>

    <!-- Include Pusher and Echo scripts -->
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@7.2.0/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.1/dist/echo.iife.min.js"></script>

    <!-- Include your admin-specific chat JS -->
    <script src="{{ asset('js/admin-chat.js') }}"></script>


    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Chats') }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-3 gap-4">
            <!-- Sidebar with Chat Sessions -->
            <div class="bg-gray-100 p-4 rounded-lg">
                <h2 class="font-bold text-lg mb-4">Messages</h2>
                @if ($chatSessions->isEmpty())
    <p class="text-gray-500">No chat sessions available.</p>
@else
    <ul>
        @foreach ($chatSessions as $session)
            <li class="mb-2">
            <a href="#" 
   onclick="console.log('Session Debug:', {{ json_encode($session) }}); loadAdminChat({{ $session->session_id ?? 'null' }})" 
   class="block bg-white p-3 rounded-lg shadow hover:bg-gray-200">

                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-300 rounded-full mr-3"></div>
                        <div>
                            <p class="font-bold">{{ $session->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $session->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </a>
            </li>
        @endforeach
    </ul>
@endif

            </div>

            <!-- Chat Messages -->
            <div class="col-span-2 bg-white p-4 rounded-lg shadow">
                <h2 class="font-bold text-lg mb-4">Chat Messages</h2>
                <div id="chat-messages" class="h-96 overflow-y-auto border rounded-lg p-4">
                    <!-- Chat messages will go here -->
                </div>

                <!-- Message Input -->
                <form id="chat-form" onsubmit="sendAdminMessage(event)" class="flex mt-4">
                    @csrf
                    <input
                        type="text"
                        id="chat-input"
                        placeholder="Type a message..."
                        class="flex-grow border rounded-l-lg px-3 py-2"
                        required
                    />
                    <button
                        type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded-r-lg hover:bg-blue-600 focus:outline-none"
                    >
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>
