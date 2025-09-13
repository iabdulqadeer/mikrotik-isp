<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class UserNotificationController extends Controller
{


    public function __construct()
    {
        $this->middleware('permission:notifications.view')->only(['index','show']);
        $this->middleware('permission:notifications.mark_read')->only(['markRead']);
        $this->middleware('permission:notifications.mark_all')->only(['markAllRead']);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        // Show newest first, paginate
        $notifications = $user->notifications()->latest()->paginate(15);

        return view('notifications.index', [
            'notifications' => $notifications,
            'unreadCount'   => $user->unreadNotifications()->count(),
        ]);
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('status', 'All notifications marked as read.');
    }

    public function markRead(Request $request, DatabaseNotification $notification)
    {
        abort_unless($notification->notifiable_id === $request->user()->id, 403);

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('status', 'Notification marked as read.');
    }

    public function show(Request $request, DatabaseNotification $notification)
    {
        abort_unless($notification->notifiable_id === $request->user()->id, 403);

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $url = data_get($notification->data, 'action_url');

        return $url ? redirect()->to($url) : redirect()->route('notifications.index');
    }
}
