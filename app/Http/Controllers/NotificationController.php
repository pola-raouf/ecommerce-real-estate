<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth');
    }

    /**
     * Display all notifications for the authenticated user.
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $this->notificationService->getPaginatedNotifications($user);
        $unreadCount = $this->notificationService->getUnreadCount($user);
        
        return view('notifications.index', compact('notifications', 'unreadCount'));
    }
    
    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $success = $this->notificationService->markNotificationAsRead($user, $id);
        
        if (!$success) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'Notification marked as read.');
    }
    
    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $this->notificationService->markAllAsRead($user);
        
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
        $count = $this->notificationService->getUnreadCount($user);
        
        return response()->json(['count' => $count]);
    }
    
    /**
     * Get recent notifications for dropdown (AJAX).
     */
    public function recent()
    {
        $user = Auth::user();
        $data = $this->notificationService->getRecentNotifications($user);
        
        return response()->json($data);
    }
}
