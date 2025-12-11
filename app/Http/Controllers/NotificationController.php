<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all notifications, ordered by newest first
        $notifications = $user->notifications()->paginate(20);
        
        // Get unread count
        $unreadCount = $user->unreadNotifications()->count();
        
        return view('notifications.index', compact('notifications', 'unreadCount'));
    }
    
    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);
        
        if ($notification) {
            $notification->markAsRead();
            
            if (request()->ajax()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->back()->with('success', 'Notification marked as read.');
        }
        
        return response()->json(['error' => 'Notification not found'], 404);
    }
    
    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
    
    /**
     * Get unread notification count (for AJAX requests).
     */
    public function unreadCount()
    {
        $user = Auth::user();
        $count = $user->unreadNotifications()->count();
        
        return response()->json(['count' => $count]);
    }
    
    /**
     * Get recent notifications for dropdown (AJAX).
     */
    public function recent()
    {
        $user = Auth::user();
        $notifications = $user->notifications()
            ->take(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'message' => $notification->data['message'] ?? 'New notification',
                    'property_id' => $notification->data['property_id'] ?? null,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }
}
