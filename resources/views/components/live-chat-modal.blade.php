<!-- resources/views/components/live-chat-modal.blade.php -->
<div id="live-chat-modal" class="hidden fixed bottom-20 right-5 bg-gray-800 text-white shadow-lg rounded-lg w-80 border border-gray-700">
    <!-- Modal Header -->
    <div class="p-3 border-b border-gray-700 flex justify-between items-center">
        <h3 class="font-bold text-lg">Live Chat</h3>
        <button
            onclick="toggleLiveChat()"
            class="text-gray-300 hover:text-red-500 focus:outline-none"
        >
            ✖
        </button>
    </div>

    <!-- Messages Section -->
    <div id="chat-messages" class="p-3 h-64 overflow-y-auto bg-gray-900 space-y-2">
        <!-- Chat messages will be dynamically added here -->
    </div>

    <!-- Input Section -->
    <div class="p-3 border-t border-gray-700">
        <form id="chat-form" onsubmit="sendMessage(event)" class="flex">
            <input
                type="text"
                id="chat-input"
                placeholder="Type a message..."
                class="flex-grow border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-l-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
            />
            <button
                type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded-r-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                Send
            </button>
        </form>
    </div>
</div>

<!-- Floating Live Chat Button -->
<div id="live-chat-button" class="fixed bottom-5 right-5">
    <button
        onclick="toggleLiveChat()"
        class="bg-blue-500 text-white px-4 py-2 rounded-full shadow-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
    >
        💬 Live Chat

    </button>
</div>
