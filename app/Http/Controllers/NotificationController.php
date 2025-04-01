<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'location' => 'required|string',
            'aqi_level' => 'required|integer'
        ]);

        $notification = Notification::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'message' => $request->message,
            'location' => $request->location,
            'aqi_level' => $request->aqi_level
        ]);

        return response()->json($notification);
    }

    public function getNotifications()
    {
        $notifications = Notification::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json($notifications);
    }

    public function getUsers()
    {
        $users = User::select('id', 'name', 'email', 'is_admin')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($users);
    }
} 