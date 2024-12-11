<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Events\MessageSent;

class ChatSessionController extends Controller
{

    public function getSessionId()
    {
        $userId = auth()->id();
    
        // Debug to ensure user ID is being fetched
        \Log::info('User ID:', ['userId' => $userId]);
    
        // Fetch or create the session
        $session = ChatSession::firstOrCreate(['user_id' => $userId]);
    
        // Debug to check if the session is created/fetched
        \Log::info('Chat Session:', ['session' => $session]);
    
        return response()->json(['sessionId' => $session->session_id]);
    }
    
    
    public function store(Request $request)
    {
        $session = ChatSession::firstOrCreate(['user_id' => auth()->id()]);
        return redirect()->route('chats.show', $session->id);
    }
    
    
    public function show($id)
    {
        $session = ChatSession::findOrFail($id);
    
        if (!auth()->user()->hasRole('admin') && $session->user_id !== auth()->id()) {
            abort(403);
        }
    
        return view('chats.show', compact('session'));
    }

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

    

    public function sendMessage(Request $request, $sessionId)
    {
    
        $request->validate(['message' => 'required|string|max:1000']);
    
        // Check if the session exists
        $session = ChatSession::findOrFail($sessionId);
    
        // Save the chat message
        $chat = Chat::create([
            'session_id' => $sessionId,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

            // Attempt to broadcast the message
            try {
                broadcast(new MessageSent($chat))->toOthers();
                \Log::info('Broadcast successful:', [
                    'channel' => "chat.{$sessionId}",
                    'message' => $chat->message,
                ]);
            } catch (\Exception $e) {
                \Log::error('Broadcasting error:', [
                    'message' => $e->getMessage(),
                    'stack' => $e->getTraceAsString(),
                ]);
                return response()->json(['error' => 'Broadcast failed'], 500);
            }
            
            \Log::info('Broadcasting MessageSent Event:', ['chat' => $chat]);

        return response()->json($chat, 201);
    }
    
    
    public function adminIndex()
    {
        $chatSessions = ChatSession::with('user')->paginate(10); // Fetch all chat sessions with user details
        \Log::info('Chat Sessions for Admin:', $chatSessions->toArray());

        return view('admin.chats.index', compact('chatSessions'));
    }
    
    public function getAdminMessages($sessionId)
    {
        $messages = Chat::where('session_id', $sessionId)
            ->with('user') // Ensure you load user information
            ->orderBy('created_at', 'asc') // Sort messages by creation time
            ->get();
    
        return response()->json($messages);
    }
    
    
    public function sendAdminMessage(Request $request, $sessionId)
    {
        \Log::info('sendAdminMessage called with sessionId:', ['sessionId' => $sessionId]);
    
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);
    
        $chat = new Chat();
        $chat->session_id = $sessionId; // Associate the session ID
        $chat->user_id = auth()->id(); // Admin's user ID
        $chat->message = $validated['message'];
        $chat->save();
    
        // Dispatch the event
        broadcast(new MessageSent($chat, auth()->user()))->toOthers();
    
        return response()->json($chat);
    }
    



}
