<?php

namespace App\Http\Controllers;

use App\DataTables\Admin\AdminsDataTable;
use App\DataTables\NotificationsDataTable;
use App\Models\Admin;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            $current_guard = Auth::getDefaultDriver();
            if ($current_guard == "web") {
                $notifiable_type = User::class;
            } elseif ($current_guard == "admin") {
                $notifiable_type = Admin::class;
            }
            $notifications = Notification::where('notifiable_type', $notifiable_type)
                ->where('notifiable_id', $user->id)
                ->orderBy('created_at', 'DESC')
                ->take(5)
                ->get();


            return response()->json([
                'status' => 'success',
                'data' => view('layouts.sections.navbar.navbar-notifications', compact('notifications'))->render(),
                'data_count' => count($notifications)
            ], 200);
        } catch (Exception $e) {
            \Log::debug($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateNotificationCount()
    {
        try {
            $user = auth()->user();
            $current_guard = Auth::getDefaultDriver();
            if ($current_guard == "web") {
                $notifiable_type = User::class;
            } elseif ($current_guard == "admin") {
                $notifiable_type = Admin::class;
            }
            Notification::where('notifiable_type', $notifiable_type)
                ->where('notifiable_id', $user->id)
                ->update(['read_at' => Carbon::now()]);

            return response()->json([
                'status' => 'success'
            ], 200);
        } catch (Exception $e) {
            \Log::debug($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function notificationTable(NotificationsDataTable $dataTable)
    {
        return $dataTable->render('pages.notifications.notifications');
    }
}
