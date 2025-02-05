<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Events\MessageSent;

class MessageController extends Controller
{
    public function index()
    {
        return Message::with(['chat', 'user'])->get();
    }

    public function store(Request $request)
    {
        // Get the authenticated user's ID
        $userid = auth()->id();

        // Validate the incoming request
        $request->validate([
            'chat_id' => 'required|exists:chats,id',  // Make sure chat exists
            'content' => 'required|string',           // Ensure content is provided and is a string
        ]);

        // Create the message, ensuring 'user_id' and 'read' are correctly handled
        $message = Message::create([
            'chat_id' => $request->chat_id,
            'user_id' => $userid, // Use the authenticated user's ID
            'content' => $request->content,
            'read' => false,       // Set the default value for 'read' to false (or 0)
        ]);

        // Broadcast the message
        broadcast(new MessageSent($message))->toOthers();


        // Return the created message as a JSON response with status code 201
        return response()->json($message, 201);
    }


    public function show($id)
    {
        return Message::with(['chat', 'user'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $message = Message::findOrFail($id);
        $request->validate([
            'content' => 'required|string',
            'read' => 'boolean',
        ]);
        $message->update($request->only('content', 'read'));
        return response()->json($message);
    }

    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();
        return response()->json(null, 204);
    }

    public function getMessagesByChatId(Request $request)
    {
        $chatId = $request->chat_Id;

        // Step 1: Retrieve messages for the given chat_id
        $messages = Message::where('chat_id', $chatId)->get();

        // Step 2: Update the "read" status of messages where the user is not the authenticated user
        $this->markMessagesAsRead($chatId, auth()->id());

        // Step 3: Return the messages as a response
        return response()->json($messages, 200);
    }

    protected function markMessagesAsRead($chatId, $authUserId)
    {
        // Update messages where user_id != authUserId and chat_id matches
        Message::where('chat_id', $chatId)
            ->where('user_id', '!=', $authUserId)
            ->where('read', 0) // Ensure we only update unread messages
            ->update(['read' => 1]); // Mark as read
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,id',
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        $message = Message::create([
            'chat_id' => $request->chat_id,
            'user_id' => $request->user_id,
            'content' => $request->content,
            'read' => false,
        ]);

        // Broadcast the message
        broadcast(new MessageSent($message))->toOthers();

        Log::info("Broadcasting message:", [$this->message]);

        return response()->json($message);
    }


    // <script src="https://js.pusher.com/7.0/pusher.min.js"></script>

    // const pusher = new Pusher('your_app_key', {
    //     cluster: 'your_app_cluster',
    // });

    // const channel = pusher.subscribe('private-chat.' + chatId); // Use the chat ID

    // channel.bind('MessageSent', function(data) {
    //     // Handle the incoming message
    //     console.log(data.message);
    //     // Update the chat UI with the new message
    // });


    // Echo.private(`chat.${chatId}`)
    // .listen('MessageSent', (e) => {
    //     console.log(e.message); // Handle the received message
    // });

    // import Echo from 'laravel-echo';
    // window.Pusher = require('pusher-js');

    // window.Echo = new Echo({
    //     broadcaster: 'pusher',
    //     key: 'your-pusher-key',
    //     cluster: 'your-cluster',
    //     encrypted: true,
    // });




}
