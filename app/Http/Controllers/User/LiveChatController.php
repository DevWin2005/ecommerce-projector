<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveChatController extends Controller
{
    /**
     * Lấy danh sách tin nhắn của Khách hàng hoặc Khách vãng lai (API Polling)
     */
    public function getMessages(Request $request)
    {
        $sessionId = session()->getId();
        $afterId = (int) $request->get('after_id', 0);

        $query = ChatMessage::query();

        if (Auth::check()) {
            $userId = Auth::id();
            $query->where(function ($q) use ($userId, $sessionId) {
                $q->where('user_id', $userId)
                  ->orWhere('session_id', $sessionId);
            });
        } else {
            $query->where('session_id', $sessionId);
        }

        $messages = $query->when($afterId > 0, function ($q) use ($afterId) {
                $q->where('id', '>', $afterId);
            })
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender_type' => $msg->sender_type,
                    'message' => $msg->message,
                    'time' => $msg->created_at->format('H:i d/m'),
                ];
            });

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Khách hàng (Auth hoặc Guest) gửi tin nhắn tới Admin
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ], [
            'message.required' => 'Vui lòng nhập nội dung tin nhắn.',
        ]);

        $sessionId = session()->getId();
        $userId = Auth::check() ? Auth::id() : null;
        $guestName = Auth::check() ? Auth::user()->name : ('Khách vãng lai #' . substr($sessionId, -4));

        $msg = ChatMessage::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'guest_name' => $guestName,
            'sender_type' => 'user',
            'message' => trim($request->message),
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $msg->id,
                'sender_type' => $msg->sender_type,
                'message' => $msg->message,
                'time' => $msg->created_at->format('H:i d/m'),
            ]
        ]);
    }
}
