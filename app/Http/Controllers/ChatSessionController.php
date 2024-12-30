<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Events\MessageSent;

class ChatSessionController extends Controller
{
    // Get or create a session for the current user
    public function getSessionId()
    {
        $userId = auth()->id();

        $session = ChatSession::firstOrCreate(['user_id' => $userId]);

        return response()->json(['sessionId' => $session->session_id]);
    }

    // Fetch all chat sessions with the latest message and time
    public function fetchSessions()
    {
        $sessions = ChatSession::with(['messages' => function ($query) {
            $query->latest()->limit(1); // Fetch only the latest message
        }, 'user'])
        ->get()
        ->map(function ($session) {
            return [
                'session_id' => $session->id,
                'user_name' => $session->user->name,
                'latest_message' => $session->messages->first()?->message ?? 'No messages yet',
                'latest_time' => $session->messages->first()?->created_at ?? null,
            ];
        });

        return response()->json($sessions);
    }

    // Fetch messages for a specific chat session
    public function fetchMessages($sessionId)
    {
        $session = ChatSession::findOrFail($sessionId);

        if (!auth()->user()->hasRole('admin') && $session->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this session.');
        }

        $messages = Chat::where('session_id', $sessionId)
            ->with('user') // Include the user who sent the message
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    // Send a message in a chat session
    public function sendMessage(Request $request, $sessionId)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $session = ChatSession::findOrFail($sessionId);

        $chat = Chat::create([
            'session_id' => $sessionId,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        try {
            broadcast(new MessageSent($chat))->toOthers();
        } catch (\Exception $e) {
            Log::error('Broadcasting error:', [
                'message' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);
        }

        return response()->json($chat, 201);
    }

    // Admin-specific: Fetch all chat sessions
    public function adminIndex()
    {
        $chatSessions = ChatSession::with([
            'chats' => function ($query) {
                $query->latest(); // Fetch only the latest message for each session
            },
            'user'
        ])->paginate(10);
    
        return view('admin.chats.index', compact('chatSessions'));
    }
    
    

    // Admin-specific: Fetch messages for a specific session
    public function getAdminMessages($sessionId)
    {
        $messages = Chat::where('session_id', $sessionId)
            ->with(['user.roles'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    // Admin-specific: Send a message in a chat session
    public function sendAdminMessage(Request $request, $sessionId)
    {
        $validated = $request->validate(['message' => 'required|string|max:1000']);

        $chat = Chat::create([
            'session_id' => $sessionId,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        broadcast(new MessageSent($chat, auth()->user()))->toOthers();

        return response()->json($chat);
    }
}
