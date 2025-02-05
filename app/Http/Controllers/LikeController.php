<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Dislike;
use App\Models\Thing;
use App\Models\Chat;
use App\Models\Notification;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function index()
    {
        $likes = Like::with(['user', 'thing'])->get();
        return response()->json($likes);
    }

    public function swipeRight(Request $request)
    {

        $request->validate([
            'thing_id' => 'required|exists:things,id',
        ]);

        $userId = auth()->id();  // Get the currently authenticated user's ID

        $like = Like::create([
            'user_id' => $userId,  // Use the authenticated user's ID
            'thing_id' => $request->thing_id,  // Get the 'thing_id' from the request
        ]);

        // Get the owner of the thing
        $thing = Thing::find($request->thing_id);
        $thingOwnerId = $thing->user_id;

        // Step 2: Check if the owner of the thing has liked anything from the user
        $hasLiked = Like::where('user_id', $thingOwnerId)
                        ->whereIn('thing_id', Thing::where('user_id', $request->user_id)->pluck('id'))
                        ->exists();

        if ($hasLiked) {
            // Step 3: Check if a chat already exists between the two users
            $chat = Chat::where(function($query) use ($request, $thingOwnerId) {
                $query->where('user1_id', $request->user_id)
                      ->where('user2_id', $thingOwnerId);
            })->orWhere(function($query) use ($request, $thingOwnerId) {
                $query->where('user1_id', $thingOwnerId)
                      ->where('user2_id', $request->user_id);
            })->first();

            // If no chat exists, create a new chat
            if (!$chat) {
                $chat = Chat::create([
                    'user1_id' => $request->user_id,
                    'user2_id' => $thingOwnerId,
                ]);
            }

            // Step 4: Create a notification for the thing owner
            Notification::create([
                'user_id' => $thingOwnerId, // The owner of the thing
                'message' => "You have a new match! Go and make an offer.",
                'is_read' => false, // Setting the notification as unread
            ]);

            // Step 4: Send response indicating a new match
            return response()->json([
                'like' => $like,
                'message' => 'Match found and chat created.',
                'match' => true,
            ], 201);
        }

        // If no match, send response without a new match indication
        return response()->json([
            'like' => $like,
            'message' => 'Like recorded, but no match found.',
            'match' => false,
        ], 201);
    }

    public function swipeLeft(Request $request)
    {
        $request->validate([
            'thing_id' => 'required|exists:things,id',
        ]);

        $userId = auth()->id();  // Get the currently authenticated user's ID

        Dislike::create([
            'user_id' => $userId,  // Use the authenticated user's ID
            'thing_id' => $request->thing_id,  // Get the 'thing_id' from the request
        ]);


        // If no match, send response without a new match indication
        return response()->json([
            'message' => 'Dislike recorded',
        ], 201);
    }



    public function show($id)
    {
        $like = Like::with(['user', 'thing'])->findOrFail($id);
        return response()->json($like);
    }

    public function destroy($id)
    {
        $like = Like::findOrFail($id);
        $like->delete();
        return response()->json(null, 204);
    }
}
