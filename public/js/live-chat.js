window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: window.PUSHER_APP_KEY, // Use the global variable
    cluster: window.PUSHER_APP_CLUSTER, // Use the global variable
    forceTLS: true,
});

console.log("Echo initialized:", window.Echo);
console.log("PUSHER_APP_KEY:", window.PUSHER_APP_KEY);
console.log("PUSHER_APP_CLUSTER:", window.PUSHER_APP_CLUSTER);

let sessionId = null; // Global variable for session ID

/**
 * Subscribe to a chat channel dynamically
 */
function subscribeToChatChannel(sessionId) {
    console.log("Subscribing to chat channel with session ID:", sessionId);

    window.Echo.private(`chat.${sessionId}`)
        .listen('MessageSent', (event) => {
            console.log('New message received:', event.message);
            appendMessage(event.message, false, event.user.name);
        });

    console.log(`Subscribed to chat channel: chat.${sessionId}`);
}

/**
 * Fetch Session ID from the server
 */
async function fetchSessionId() {
    console.log("Fetching session ID...");
    try {
        const response = await fetch("/chat/session-id", {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
        });

        if (!response.ok) {
            throw new Error("Failed to fetch session ID from the server.");
        }

        const data = await response.json();
        console.log("Session ID fetched:", data.sessionId);

        sessionId = data.sessionId; // Set the global sessionId
        subscribeToChatChannel(sessionId); // Subscribe to the channel
        return sessionId;
    } catch (error) {
        console.error("Failed to fetch session ID:", error);
        return null; // Return null if the fetch fails
    }
}

/**
 * Toggle Live Chat Modal Visibility
 */
async function toggleLiveChat() {
    const modal = document.getElementById("live-chat-modal");
    const isHidden = modal.classList.contains("hidden");

    if (isHidden) {
        // Fetch session ID if it's not already set
        if (!sessionId) {
            sessionId = await fetchSessionId();
            if (!sessionId) {
                console.error("Session ID could not be fetched. Cannot open chat.");
                return; // Prevent opening the chat if no session ID
            }
        }

        loadChatHistory(sessionId); // Load chat history when opening the modal
    }

    modal.classList.toggle("hidden"); // Open or close the modal
}

// Attach to the global scope
window.toggleLiveChat = toggleLiveChat;

/**
 * Load Chat History for a Given Session
 */
async function loadChatHistory(sessionId) {
    console.log("Loading chat history for session ID:", sessionId);
    const chatMessagesContainer = document.getElementById("chat-messages");
    chatMessagesContainer.innerHTML = ""; // Clear existing messages

    try {
        const response = await fetch(`/chats/${sessionId}/messages`);
        if (!response.ok) {
            throw new Error("Failed to fetch messages.");
        }

        const messages = await response.json();
        console.log("Fetched messages:", messages);

        if (messages.length === 0) {
            chatMessagesContainer.innerHTML = "<p class='text-gray-400'>No messages yet.</p>";
        }

        // Append each message to the chat window
        messages.forEach(chat => {
            appendMessage(chat.message, chat.user_id === CURRENT_USER_ID, chat.user.name);
        });
    } catch (error) {
        console.error("Failed to load chat history:", error);
    }
}

/**
 * Send a New Message
 */
async function sendMessage(event) {
    event.preventDefault();

    const input = document.getElementById("chat-input");
    const message = input.value.trim();

    if (!message) {
        console.error("Message is empty.");
        input.value = ""; // Clear accidental spaces
        return;
    }

    if (!sessionId) {
        console.error("Session ID is not defined.");
        return;
    }

    try {
        const response = await fetch(`/chats/${sessionId}/message`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ message }),
        });

        if (!response.ok) {
            throw new Error(`Failed to send message: ${response.status} ${response.statusText}`);
        }

        const chat = await response.json();
        console.log("Message sent and saved:", chat);

        appendMessage(chat.message, true); // Add the sent message to the chat window
        input.value = ""; // Clear the input field
    } catch (error) {
        console.error("Error sending message:", error);
    }
}
window.sendMessage = sendMessage;

/**
 * Append a Message to the Chat Window
 */
function appendMessage(message, isUser, sender = "Admin") {
    console.log("Appending message:", { message, isUser, sender }); // Debug log

    const messagesContainer = document.getElementById("chat-messages");

    // Add sender name for messages from others
    if (!isUser) {
        const senderDiv = document.createElement("div");
        senderDiv.classList.add("text-xs", "text-gray-400", "mb-1");
        senderDiv.textContent = sender;
        messagesContainer.appendChild(senderDiv);
    }

    // Create a new message div
    const messageDiv = document.createElement("div");
    messageDiv.classList.add("flex", isUser ? "justify-end" : "justify-start");

    // Create the message bubble
    const bubble = document.createElement("div");
    bubble.classList.add(
        "px-4",
        "py-2",
        "rounded-lg",
        "max-w-xs",
        "break-words",
        isUser ? "bg-blue-500" : "bg-gray-700", // Conditional background color
        "text-white" // Shared text color for both conditions
    );
    bubble.textContent = message;

    // Append the bubble to the message div
    messageDiv.appendChild(bubble);

    // Append the message div to the container
    messagesContainer.appendChild(messageDiv);

    // Scroll to the bottom of the container
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}
