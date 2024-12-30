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
            if (!response.ok) {
                throw new Error('Failed to fetch messages');
            }
            return response.json();
        })
        .then(messages => {
            messages.forEach(chat => {
                // Check for admin role (case-insensitive)
                const isAdmin = chat.user.roles && chat.user.roles.some(role => role.type.toLowerCase() === 'admin');
                const isCurrentUser = chat.user_id === CURRENT_USER_ID;

                // Debug logs
                console.log('Message:', chat.message);
                console.log('Sender:', chat.user.name);
                console.log('Roles:', chat.user.roles || 'Roles not provided');
                console.log('isAdmin:', isAdmin, 'isCurrentUser:', isCurrentUser);

                appendMessage(chat.message, isCurrentUser, chat.user.name, isAdmin);
            });

        })
        .catch(error => console.error('Error loading chat messages:', error));

    // Set up real-time listener for this session
    window.Echo.private(`chat.${sessionId}`)
        .listen('MessageSent', (e) => {
            const isAdmin = e.user.roles && e.user.roles.some(role => role.type === 'admin');
            const isCurrentUser = e.user.id === CURRENT_USER_ID;

            console.log('Realtime Message:', e.message.message);
            console.log('Sender:', e.user.name, 'Roles:', e.user.roles, 'isAdmin:', isAdmin);

            appendMessage(e.message.message, isCurrentUser, e.user.name, isAdmin);
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
    appendMessage(message, true, 'You', true); // Explicitly mark it as the current admin

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

function appendMessage(message, isUser, sender = 'User', isAdmin = false) {
    const messagesContainer = document.getElementById('chat-messages');

    // Create container for the message
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('mb-2');

    // Add sender's name (only for messages not sent by the current user)
    if (!isUser) {
        const senderName = document.createElement('span');
        senderName.classList.add('block', 'text-sm', 'text-gray-500', 'mb-1');
        senderName.textContent = sender; // Display the sender's name
        messageDiv.appendChild(senderName);
    }

    // Style the chat bubble
    const bubble = document.createElement('div');
    bubble.classList.add('inline-block', 'px-4', 'py-2', 'rounded-lg', 'max-w-xs', 'break-words');
    bubble.style.backgroundColor = isUser || isAdmin ? '#3b82f6' : '#e5e7eb'; // Blue for admin or user, gray for others
    bubble.style.color = isUser || isAdmin ? '#ffffff' : '#000000';
    bubble.textContent = message;

    // Align bubble
    if (isUser || isAdmin) {
        messageDiv.classList.add('text-right'); // Align admin and current user messages to the right
    } else {
        messageDiv.classList.add('text-left'); // Align non-admin user messages to the left
    }

    messageDiv.appendChild(bubble);
    messagesContainer.appendChild(messageDiv);

    // Scroll to the bottom
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}
