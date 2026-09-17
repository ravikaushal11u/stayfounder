<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chat\StoreMessageRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatController extends Controller
{
    /**
     * Display the chat inbox with active conversation.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Fetch all conversations where current user is student or provider
        $conversations = Conversation::where(function ($q) use ($user) {
            $q->where('student_id', $user->id)
              ->orWhere('provider_id', $user->id);
        })
        ->with(['property.images', 'student.studentProfile', 'provider.providerProfile', 'latestMessage'])
        ->orderByDesc('last_message_at')
        ->get();

        $activeConversationId = $request->query('conversation_id');
        $activeConversation = null;

        if ($activeConversationId) {
            $activeConversation = $conversations->firstWhere('id', (int) $activeConversationId);
        }

        if (! $activeConversation && $conversations->isNotEmpty()) {
            $activeConversation = $conversations->first();
        }

        // If an active conversation is selected, load its messages and mark unread as read
        if ($activeConversation) {
            // Security check
            if ($activeConversation->student_id !== $user->id && $activeConversation->provider_id !== $user->id) {
                abort(403);
            }

            $activeConversation->load(['messages.sender', 'property', 'student', 'provider.providerProfile']);

            // Mark unread messages sent by the other party as read
            $activeConversation->messages()
                ->where('sender_id', '!=', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return view('chat.index', compact('conversations', 'activeConversation'));
    }

    /**
     * Fetch conversation messages (supports polling / AJAX).
     */
    public function show(Request $request, Conversation $conversation): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        if ($conversation->student_id !== $user->id && $conversation->provider_id !== $user->id) {
            abort(403);
        }

        // Mark incoming messages as read
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($request->expectsJson()) {
            $messages = $conversation->messages()->with('sender:id,name,role')->get();
            return response()->json([
                'conversation_id' => $conversation->id,
                'messages' => $messages,
            ]);
        }

        return redirect()->route('chat.index', ['conversation_id' => $conversation->id]);
    }

    /**
     * Send a new message within a conversation.
     */
    public function store(StoreMessageRequest $request, Conversation $conversation): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        if ($conversation->student_id !== $user->id && $conversation->provider_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validated();

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'body' => trim($validated['body']),
        ]);

        $conversation->update(['last_message_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('sender:id,name,role'),
            ]);
        }

        return redirect()->route('chat.index', ['conversation_id' => $conversation->id]);
    }

    /**
     * Start a new chat thread from a property listing.
     */
    public function start(Request $request, Property $property): RedirectResponse
    {
        $user = $request->user();

        if ($property->user_id === $user->id) {
            return back()->with('warning', 'You cannot start a chat thread on your own accommodation listing.');
        }

        $conversation = Conversation::firstOrCreate(
            [
                'property_id' => $property->id,
                'student_id' => $user->id,
                'provider_id' => $property->user_id,
            ],
            [
                'last_message_at' => now(),
            ]
        );

        // Optional first message
        if ($request->filled('message')) {
            $conversation->messages()->create([
                'sender_id' => $user->id,
                'body' => trim($request->input('message')),
            ]);
            $conversation->update(['last_message_at' => now()]);
        }

        return redirect()->route('chat.index', ['conversation_id' => $conversation->id]);
    }
}
