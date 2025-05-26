<?php

namespace App\Http\Controllers\API\Chats;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ChatApiController extends Controller
{
    public function fetchUpdates(Request $request)
{
    $authUserId = $request->user()->id;
    $lastMessageId = (int) $request->query('last_message_id', 0);

    // Step 1: Get open conversation IDs
    $openConversations = DB::table('messages_live_chats_conversations as conv')
        ->join('messages_live_chats as msgs', 'conv.id', '=', 'msgs.conversation_id')
        ->whereIn('conv.status', ['open', 'pending'])
        ->where(function ($query) use ($authUserId) {
            $query->where('msgs.sender_id', $authUserId)
                ->orWhere('msgs.receiver_id', $authUserId);
        })
        ->select('conv.id')
        ->distinct()
        ->pluck('conv.id')
        ->toArray();

    $latestConversations = collect();
    $newMessages = collect();

    if (!empty($openConversations)) {
        // Step 2: Fetch latest message per conversation (even if already fetched)
        $latestConversations = DB::table('messages_live_chats as m1')
    ->join(DB::raw('(
        SELECT MAX(id) as max_id
        FROM messages_live_chats
        WHERE conversation_id IN (' . implode(',', $openConversations) . ')
        AND api_status = 0
        GROUP BY conversation_id
    ) as m2'), 'm1.id', '=', 'm2.max_id')
    ->join('users as u', 'u.id', '=', 'm1.sender_id')
    ->select(
        'm1.*',
        'm1.api_status',
        'u.firstname as sender_firstname',
        'u.lastname as sender_lastname',
        'u.photo as sender_photo'
    )
    ->orderBy('m1.sent_at', 'desc')
    ->get();


        // Add base64 photo
        foreach ($latestConversations as $message) {
            $photoPath = $message->sender_photo ?? null;
            $photoData = file_exists(storage_path('app/public/' . $photoPath ?? '')) ?
                file_get_contents(storage_path('app/public/' . $photoPath)) :
                file_get_contents(public_path('assets/images/users/avatar-1.jpg'));

            $message->base64_photo = 'data:image/jpeg;base64,' . base64_encode($photoData);
        }

        // Step 3: Fetch all unfetched messages (api_status = 0)
        $newMessages = DB::table('messages_live_chats')
            ->whereIn('conversation_id', $openConversations)
            ->where('api_status', 0)
            ->orderBy('sent_at')
            ->get();

        // Step 4: Mark fetched messages as api_status = 1
        if ($newMessages->isNotEmpty()) {
            $messageIds = $newMessages->pluck('id')->toArray();

            DB::table('messages_live_chats')
                ->whereIn('id', $messageIds)
                ->update(['api_status' => 1]);
        }

    }

    return response()->json([
        'status' => 'success',
        'latestConversations' => $latestConversations,
        'newMessages' => $newMessages,
    ]);
}





public function loadConversation($conversation_id)
{
    $userId = auth()->id();

    // Only return messages where the user is either sender or receiver in that conversation
    $messages = DB::table('messages_live_chats')
        ->where('conversation_id', $conversation_id)
        ->where(function ($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
        })
        ->orderBy('sent_at', 'asc')
        ->get();

    // Optional: get the name and photo of the other user
    $otherUser = DB::table('users')
        ->join('messages_live_chats', function ($join) use ($conversation_id) {
            $join->on('users.id', '=', DB::raw("
                CASE 
                    WHEN messages_live_chats.sender_id = " . auth()->id() . " 
                    THEN messages_live_chats.receiver_id 
                    ELSE messages_live_chats.sender_id 
                END
            "));
        })
        ->where('messages_live_chats.conversation_id', $conversation_id)
        ->select('users.firstname', 'users.lastname', 'users.photo')
        ->first();

    return response()->json([
        'status' => 'success',
        'messages' => $messages,
        'user' => $otherUser
    ]);
}


}
