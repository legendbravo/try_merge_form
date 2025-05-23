<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'barangay') {
            return response()->json(['error' => 'Unauthorized or invalid user role.'], 403);
        }

        $notifications = Notification::where('user_id', $user->id)
            // ->where('is_read', false) // Initially, we might fetch all and let frontend handle unread badge
            ->orderBy('created_at', 'desc')
            ->get();

        $notifications = $notifications->map(function ($n) {
            $report = $n->report;
            if ($report) {
                $uniqueId = $report->frequency . '_' . $report->id;
                $url = url('/barangay/submissions?open_remarks=' . $uniqueId);
            } else {
                $url = '#';
            }
            return [
                'id' => $n->id,
                'report_id' => $n->report_id,
                'message' => $n->message,
                'is_read' => $n->is_read,
                'created_at' => $n->created_at,
                'time_ago' => $n->created_at ? $n->created_at->diffForHumans() : '',
                'url' => $url,
            ];
        });

        $unreadCount = $notifications->where('is_read', false)->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark specified notifications as read for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'barangay') {
            return response()->json(['error' => 'Unauthorized or invalid user role.'], 403);
        }

        $notificationIds = $request->input('ids');

        if (empty($notificationIds)) {
            // Mark all unread notifications as read if no specific IDs are provided (e.g., when dropdown opens)
            Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => Carbon::now()
                ]);
            return response()->json(['message' => 'All unread notifications marked as read.']);
        } else {
            // Mark specific notifications as read
            Notification::where('user_id', $user->id)
                ->whereIn('id', $notificationIds)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => Carbon::now()
                ]);
            return response()->json(['message' => 'Selected notifications marked as read.']);
        }
    }

    /**
     * Remove the specified notification for a given report_id after resubmission.
     * This should be called internally or via a specific route after successful report resubmission.
     *
     * @param  int  $reportId
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearNotificationForReport(int $reportId)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'barangay') {
            return response()->json(['error' => 'Unauthorized or invalid user role.'], 403);
        }

        $deletedCount = Notification::where('user_id', $user->id)
            ->where('report_id', $reportId)
            ->delete();

        if ($deletedCount > 0) {
            return response()->json(['message' => 'Notification cleared successfully.']);
        } else {
            return response()->json(['message' => 'No notification found for this report or already cleared.'], 404);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not typically used for system-generated notifications
        return response()->json(['message' => 'Not applicable'], 405);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Notifications are created by the system (e.g., Admin action), not directly by user via API call typically.
        // This might be used if an admin interface triggers notification creation directly.
        // For now, focusing on Barangay user interactions.
        return response()->json(['message' => 'Not applicable for direct user creation'], 405);
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification)
    {
        $user = Auth::user();
        if (!$user || $user->id !== $notification->user_id || $user->role !== 'barangay') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json($notification);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification)
    {
        // Not typically used
        return response()->json(['message' => 'Not applicable'], 405);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notification $notification)
    {
        // Marking as read is handled by markAsRead method.
        // Other updates are not typical for this flow.
        return response()->json(['message' => 'Not applicable. Use markAsRead.'], 405);
    }

    /**
     * Remove the specified resource from storage.
     * This is for direct deletion of a notification by its ID.
     * For clearing based on report_id, use clearNotificationForReport.
     */
    public function destroy(Notification $notification)
    {
        $user = Auth::user();
        if (!$user || $user->id !== $notification->user_id || $user->role !== 'barangay') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();
        return response()->json(['message' => 'Notification deleted successfully.']);
    }
}
