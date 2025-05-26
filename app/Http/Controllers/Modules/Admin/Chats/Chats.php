<?php

namespace App\Http\Controllers\Modules\Admin\Chats;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\NewMessageSent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Chats extends Controller
{

public function index()
{
    $authUserId = auth()->id();

    // === STEP 1: OPEN + PENDING ===
    $conversationIds = DB::table('messages_live_chats_conversations as conv')
        ->join('messages_live_chats as msgs', 'conv.id', '=', 'msgs.conversation_id')
        ->whereIn('conv.status', ['open', 'pending'])
        ->where(function ($query) use ($authUserId) {
            $query->where('msgs.sender_id', $authUserId)
                  ->orWhere('msgs.receiver_id', $authUserId);
        })
        ->distinct()
        ->pluck('conv.id')
        ->toArray();

    $latestMessages = [];

    if (!empty($conversationIds)) {
        $latestMessages = DB::table('messages_live_chats as m1')
            ->select('m1.*', 'u.firstname as sender_firstname', 'u.lastname as sender_lastname')
            ->join('users as u', 'm1.sender_id', '=', 'u.id')
            ->join(DB::raw('
                (SELECT MAX(id) as max_id
                 FROM messages_live_chats
                 WHERE conversation_id IN (' . implode(',', $conversationIds) . ')
                 GROUP BY conversation_id) as m2
            '), 'm1.id', '=', 'm2.max_id')
            ->orderBy('m1.sent_at', 'desc')
            ->get();

        foreach ($latestMessages as $message) {
            $sender = DB::table('users')->find($message->sender_id);
            $photoPath = $sender->photo ?? null;

            if ($photoPath && file_exists(storage_path('app/public/' . $photoPath))) {
                $photoData = file_get_contents(storage_path('app/public/' . $photoPath));
            } else {
                $photoData = file_get_contents(public_path('assets/images/users/avatar-1.jpg'));
            }

            $message->base64_photo = 'data:image/jpeg;base64,' . base64_encode($photoData);
        }
    }

    // === STEP 2: CLOSED ===
    $closedConversationIds = DB::table('messages_live_chats_conversations as conv')
        ->join('messages_live_chats as msgs', 'conv.id', '=', 'msgs.conversation_id')
        ->where('conv.status', 'closed')
        ->where(function ($query) use ($authUserId) {
            $query->where('msgs.sender_id', $authUserId)
                  ->orWhere('msgs.receiver_id', $authUserId);
        })
        ->distinct()
        ->pluck('conv.id')
        ->toArray();

    $closedMessages = [];

    if (!empty($closedConversationIds)) {
        $closedMessages = DB::table('messages_live_chats as m1')
            ->select('m1.*', 'u.firstname as sender_firstname', 'u.lastname as sender_lastname')
            ->join('users as u', 'm1.sender_id', '=', 'u.id')
            ->join(DB::raw('
                (SELECT MAX(id) as max_id
                 FROM messages_live_chats
                 WHERE conversation_id IN (' . implode(',', $closedConversationIds) . ')
                 GROUP BY conversation_id) as m2
            '), 'm1.id', '=', 'm2.max_id')
            ->orderBy('m1.sent_at', 'desc')
            ->get();

        foreach ($closedMessages as $message) {
            $sender = DB::table('users')->find($message->sender_id);
            $photoPath = $sender->photo ?? null;

            if ($photoPath && file_exists(storage_path('app/public/' . $photoPath))) {
                $photoData = file_get_contents(storage_path('app/public/' . $photoPath));
            } else {
                $photoData = file_get_contents(public_path('assets/images/users/avatar-1.jpg'));
            }

            $message->base64_photo = 'data:image/jpeg;base64,' . base64_encode($photoData);
        }
    }

    //return under maintainance
    abort(503, 'Under Maintenance');

    // === RETURN TO VIEW ===
    // return view('modules.administrator.chat.index', compact('latestMessages', 'closedMessages'));
}











    public function send(Request $request)
    {
        $data = [
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'sent_at' => now(),
        ];

        $id = DB::table('messages_live_chats')->insertGetId($data);
        $data['id'] = $id;

        broadcast(new NewMessageSent((object)$data))->toOthers();

        return response()->json(['status' => 'Message sent']);
    }
}
