<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LiveChatController extends Controller
{
    /**
     * Giao diện trung tâm LiveChat Admin
     */
    public function index()
    {
        return view('admin.livechat.index');
    }

    /**
     * Trả về danh sách tất cả cuộc hội thoại (Khách hàng & Khách vãng lai)
     */
    public function getConversations()
    {
        // Nhóm theo user_id hoặc session_id
        $groups = ChatMessage::select(
                'user_id',
                'session_id',
                DB::raw('MAX(id) as max_id')
            )
            ->groupBy('user_id', 'session_id')
            ->orderByDesc('max_id')
            ->get();

        $conversations = $groups->map(function ($g) {
            $latestMsg = ChatMessage::find($g->max_id);

            if (!$latestMsg) {
                return null;
            }

            $chatKey = $g->user_id ? ('user_' . $g->user_id) : ('guest_' . $g->session_id);
            $userName = 'Khách vãng lai';
            $userEmail = 'Khách truy cập website';

            if ($g->user_id) {
                $userObj = User::find($g->user_id);
                if ($userObj) {
                    $userName = $userObj->name;
                    $userEmail = $userObj->email;
                }
            } else {
                $userName = $latestMsg->guest_name ?? ('Khách vãng lai #' . substr($g->session_id, -4));
            }

            // Đếm số tin nhắn chưa đọc từ phía Khách
            $unreadQuery = ChatMessage::where('sender_type', 'user')->where('is_read', false);
            if ($g->user_id) {
                $unreadQuery->where('user_id', $g->user_id);
            } else {
                $unreadQuery->where('session_id', $g->session_id)->whereNull('user_id');
            }
            $unreadCount = $unreadQuery->count();

            return [
                'chat_key' => $chatKey,
                'user_id' => $g->user_id,
                'session_id' => $g->session_id,
                'user_name' => $userName,
                'user_email' => $userEmail,
                'unread_count' => $unreadCount,
                'latest_message' => $latestMsg->message,
                'latest_time' => $latestMsg->created_at->format('H:i d/m'),
                'timestamp' => $latestMsg->created_at->timestamp,
            ];
        })->filter()->values();

        return response()->json([
            'success' => true,
            'conversations' => $conversations,
        ]);
    }

    /**
     * Lấy lịch sử tin nhắn của cuộc hội thoại (API)
     */
    public function getMessages($chatKey, Request $request)
    {
        $afterId = (int) $request->get('after_id', 0);
        $query = ChatMessage::query();

        if (str_starts_with($chatKey, 'user_')) {
            $userId = (int) str_replace('user_', '', $chatKey);
            $query->where('user_id', $userId);

            // Đánh dấu đã đọc
            ChatMessage::where('user_id', $userId)->where('sender_type', 'user')->where('is_read', false)->update(['is_read' => true]);
            $userObj = User::find($userId);
            $chatInfo = [
                'name' => $userObj ? $userObj->name : 'Khách hàng',
                'email' => $userObj ? $userObj->email : '',
            ];
        } else {
            $sessionId = str_replace('guest_', '', $chatKey);
            $query->where('session_id', $sessionId)->whereNull('user_id');

            // Đánh dấu đã đọc
            ChatMessage::where('session_id', $sessionId)->whereNull('user_id')->where('sender_type', 'user')->where('is_read', false)->update(['is_read' => true]);

            $latestMsg = ChatMessage::where('session_id', $sessionId)->first();
            $chatInfo = [
                'name' => $latestMsg ? ($latestMsg->guest_name ?? 'Khách vãng lai') : 'Khách vãng lai',
                'email' => 'Khách truy cập website (Chưa đăng nhập)',
            ];
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
            'chat_info' => $chatInfo,
            'messages' => $messages,
        ]);
    }

    /**
     * Admin gửi tin nhắn trả lời cuộc hội thoại (API)
     */
    public function sendMessage($chatKey, Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ], [
            'message.required' => 'Vui lòng nhập nội dung tin nhắn.',
        ]);

        $userId = null;
        $sessionId = null;

        if (str_starts_with($chatKey, 'user_')) {
            $userId = (int) str_replace('user_', '', $chatKey);
        } else {
            $sessionId = str_replace('guest_', '', $chatKey);
        }

        $msg = ChatMessage::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'admin_id' => Auth::id(),
            'sender_type' => 'admin',
            'message' => trim($request->message),
            'is_read' => true,
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

    /**
     * Tìm kiếm tài khoản Khách hàng để Admin chủ động chọn nhắn tin
     */
    public function searchUsers(Request $request)
    {
        $query = trim($request->get('q', ''));
        $users = User::where('role', 'user')
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->latest()
            ->take(20)
            ->get(['id', 'name', 'email']);

        return response()->json([
            'success' => true,
            'users' => $users,
        ]);
    }
}
