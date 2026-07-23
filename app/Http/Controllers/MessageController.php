<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;

class MessageController extends Controller
{
    public function getMessages(Request $request)
    {
        $user = $request->user();

        $inbox = Message::with('sender')
            ->where('receiver_id', $user->id)
            ->latest()
            ->get()
            ->map(function($msg) {
                return [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'sender_name' => $msg->sender->name,
                    'subject' => $msg->subject,
                    'body' => $msg->body,
                    'is_read' => $msg->is_read,
                    'created_at' => $msg->created_at,
                ];
            });

        $sent = Message::with('receiver')
            ->where('sender_id', $user->id)
            ->latest()
            ->get()
            ->map(function($msg) {
                return [
                    'id' => $msg->id,
                    'receiver_id' => $msg->receiver_id,
                    'receiver_name' => $msg->receiver->name,
                    'subject' => $msg->subject,
                    'body' => $msg->body,
                    'is_read' => $msg->is_read,
                    'created_at' => $msg->created_at,
                ];
            });

        $unreadCount = Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'inbox' => $inbox,
            'sent' => $sent,
            'unread_count' => $unreadCount,
        ]);
    }

    public function getConversation(Request $request, $userId)
    {
        $user = $request->user();
        $otherUser = User::find($userId);

        if (!$otherUser) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $messages = Message::where(function($q) use ($user, $userId) {
                $q->where('sender_id', $user->id)->where('receiver_id', $userId);
            })
            ->orWhere(function($q) use ($user, $userId) {
                $q->where('sender_id', $userId)->where('receiver_id', $user->id);
            })
            ->latest()
            ->get()
            ->map(function($msg) use ($user) {
                return [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'is_mine' => $msg->sender_id === $user->id,
                    'subject' => $msg->subject,
                    'body' => $msg->body,
                    'is_read' => $msg->is_read,
                    'created_at' => $msg->created_at,
                ];
            });

        // Mark as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'messages' => $messages,
            'other_user' => [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
            ]
        ]);
    }

    public function sendMessage(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'body' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'subject' => $request->subject ?? 'No Subject',
            'body' => $request->body,
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $message = Message::find($id);
        if ($message) {
            $message->update(['is_read' => true]);
        }
        return response()->json(['message' => 'Marked as read']);
    }

    public function deleteMessage(Request $request, $id)
    {
        $user = $request->user();
        $message = Message::where('id', $id)
            ->where(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
            })->first();

        if (!$message) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        $message->delete();
        return response()->json(['message' => 'Message deleted']);
    }
}