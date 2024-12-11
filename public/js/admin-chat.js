window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: window.PUSHER_APP_KEY, // Use the global variable
    cluster: window.PUSHER_APP_CLUSTER, // Use the global variable
    forceTLS: true,
});

console.log("Echo initialized:", window.Echo);

let currentSessionId = null;

// Subscribe to the chat session dynamically
function loadAdminChat(sessionId) {
    console.log('Loading chat for session ID:', sessionId); // Debug log
    currentSessionId = sessionId;
    const chatMessagesContainer = document.getElementById('chat-messages');
    chatMessagesContainer.innerHTML = ''; // Clear previous messages

    // Fetch messages from the server
    fetch(`/admin/chats/${sessionId}/messages`)
        .then(response => {
            if (!response.ok) throw new Error('Failed to load chat messages.');
            return response.json();
        })
        .then(messages => {
            messages.forEach(chat => {
                appendMessage(chat.message, chat.user_id === CURRENT_USER_ID, chat.user.name);
            });
        })
        .catch(error => {
            console.error('Error loading chat messages:', error);
            chatMessagesContainer.innerHTML = '<p class="text-gray-500">Failed to load messages.</p>';
        });

    // Ensure listener is set for this session
    window.Echo.private(`chat.${sessionId}`)
        .listen('MessageSent', (e) => {
            console.log('Message received:', e.message);
            appendMessage(e.message.message, false, e.user.name);
        });
}

window.loadAdminChat = loadAdminChat;

function sendAdminMessage(event) {
    event.preventDefault();
    const input = document.getElementById('chat-input');
    const message = input.value.trim();

    if (!message) {
        console.error('Message is empty.');
        input.value = ''; // Clear accidental spaces
        return;
    }

    if (!currentSessionId) {
        console.error('Session ID is not set.');
        return;
    }

    // Append the message locally (optimistic UI)
    appendMessage(message, true);

    // Send the message to the server
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/admin/chats/${currentSessionId}/message`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ message }),
    })
        .then(response => {
            if (!response.ok) throw new Error('Failed to send message.');
            return response.json();
        })
        .then(data => {
            console.log('Message saved successfully:', data);
            input.value = ''; // Clear the input
        })
        .catch(error => {
            console.error('Error sending message:', error);
            alert('Failed to send message. Please try again.');
        });
}

window.sendAdminMessage = sendAdminMessage;

function appendMessage(message, isUser, sender = 'User') {
    const messagesContainer = document.getElementById('chat-messages');

    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('mb-2', isUser ? 'text-right' : 'text-left');

    const bubble = document.createElement('div');
    bubble.classList.add('inline-block', 'px-4', 'py-2', 'rounded-lg', 'max-w-xs', 'break-words');
    bubble.style.backgroundColor = isUser ? '#3b82f6' : '#e5e7eb'; // Blue for admin, gray for user
    bubble.style.color = isUser ? '#ffffff' : '#000000';
    bubble.textContent = message;

    messageDiv.appendChild(bubble);
    messagesContainer.appendChild(messageDiv);

    // Scroll to the bottom
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}
