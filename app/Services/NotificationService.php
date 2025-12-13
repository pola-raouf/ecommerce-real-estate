<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;

class NotificationService
{
    protected Logger $logger;

    public function __construct()
    {
        $this->logger = Logger::getInstance();
    }

    /**
     * Get paginated notifications for a user
     */
    public function getPaginatedNotifications(User $user, int $perPage = 20)
    {
        return $user->notifications()->paginate($perPage);
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * Mark a specific notification as read
     */
    public function markNotificationAsRead(User $user, string $notificationId): bool
    {
        $notification = $user->notifications()->find($notificationId);
        
        if (!$notification) {
            $this->logger->warning('Notification not found', [
                'user_id' => $user->id,
                'notification_id' => $notificationId,
            ]);
            return false;
        }

        $notification->markAsRead();

        $this->logger->info('Notification marked as read', [
            'user_id' => $user->id,
            'notification_id' => $notificationId,
        ]);

        return true;
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(User $user): void
    {
        $count = $user->unreadNotifications()->count();
        $user->unreadNotifications->markAsRead();

        $this->logger->info('All notifications marked as read', [
            'user_id' => $user->id,
            'count' => $count,
        ]);
    }

    /**
     * Get recent notifications (for dropdown/AJAX)
     */
    public function getRecentNotifications(User $user, int $limit = 5): array
    {
        $notifications = $user->notifications()
            ->take($limit)
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

        return [
            'notifications' => $notifications,
            'unread_count' => $this->getUnreadCount($user),
        ];
    }
}
