<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markRead(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        \DB::table('notif_table')
            ->where('added_by_id', $user->studentID)
            ->where('status_from_added', 'unchecked')
            ->update(['status_from_added' => 'checked']);

        \DB::table('notif_table')
            ->where('target_by_id', $user->studentID)
            ->where('status_from_target', 'unchecked')
            ->update(['status_from_target' => 'checked']);

        return response()->json(['success' => true]);
    }

}