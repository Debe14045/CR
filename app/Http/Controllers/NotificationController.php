<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display the main notifications center page
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $baseQuery = Notification::forUser($user)->with('changeRequest');

        // Counts
        $totalCount = (clone $baseQuery)->count();
        $unreadCount = (clone $baseQuery)->unread()->count();

        // Filter by Tab (Semua vs Belum Dibaca)
        $tab = $request->query('tab', 'all');
        $query = clone $baseQuery;

        if ($tab === 'unread') {
            $query->unread();
        }

        // Search query filter
        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('changeRequest', function ($crQuery) use ($search) {
                      $crQuery->where('kode_cr', 'like', "%{$search}%")
                              ->orWhere('judul', 'like', "%{$search}%");
                  });
            });
        }

        // Order by latest first
        $notifications = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        return view('notifications.index', compact(
            'notifications',
            'tab',
            'totalCount',
            'unreadCount'
        ));
    }

    /**
     * Mark notification as read and redirect directly to its target CR
     */
    public function go($id)
    {
        $user = Auth::user();
        $notification = Notification::forUser($user)->findOrFail($id);
        $notification->markAsRead();

        $targetUrl = $notification->action_url;
        if (!$targetUrl && $notification->change_request_id) {
            $targetUrl = route('change-requests.show', $notification->change_request_id);
        }

        return redirect($targetUrl ?: route('notifications.index'));
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        $notification = Notification::forUser($user)->findOrFail($id);
        $notification->markAsRead();

        if ($request->wantsJson() || $request->ajax()) {
            $unreadCount = Notification::forUser($user)->unread()->count();
            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount,
                'message' => 'Notifikasi ditandai sudah dibaca'
            ]);
        }

        return back()->with('success', 'Notifikasi berhasil ditandai sudah dibaca.');
    }

    /**
     * Mark all notifications as read for current user
     */
    public function markAllRead(Request $request)
    {
        $user = Auth::user();
        Notification::forUser($user)->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
                'message' => 'Semua notifikasi berhasil ditandai sudah dibaca'
            ]);
        }

        return back()->with('success', 'Semua notifikasi berhasil ditandai sudah dibaca.');
    }

    /**
     * Delete a single notification
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $notification = Notification::forUser($user)->findOrFail($id);
        $notification->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Clear all read notifications
     */
    public function clearRead(Request $request)
    {
        $user = Auth::user();
        Notification::forUser($user)->read()->delete();

        return back()->with('success', 'Riwayat notifikasi yang telah dibaca berhasil dibersihkan.');
    }

    /**
     * JSON endpoint for top navbar bell dropdown & live updates
     */
    public function apiRecent(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['unread_count' => 0, 'notifications' => []]);
        }

        $unreadCount = Notification::forUser($user)->unread()->count();
        $recent = Notification::forUser($user)
            ->with('changeRequest')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($notif) {
                $theme = $notif->getColorTheme();
                return [
                    'id' => $notif->id,
                    'title' => $notif->title,
                    'message' => $notif->message,
                    'is_read' => $notif->is_read,
                    'time_ago' => $notif->formatted_time,
                    'action_url' => $notif->action_url ?: ($notif->change_request_id ? route('change-requests.show', $notif->change_request_id) : '#'),
                    'icon' => $notif->icon,
                    'theme' => $theme,
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $recent,
        ]);
    }
}
