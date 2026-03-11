<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    public function store(Request $request)
    {
        abort_unless(in_array(Auth::user()->role, ['chair', 'admin']), 403);

        $data = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'class_offering_id' => 'nullable|exists:class_offerings,id',
            'message' => 'nullable|string|max:255',
        ]);

        Reminder::create([
            'recipient_id' => $data['recipient_id'],
            'class_offering_id' => $data['class_offering_id'],
            'sender_id' => Auth::id(),
            'message' => $data['message'] ?? 'Please update your portfolio.',
        ]);

        return back()->with('status', 'Reminder sent successfully!');
    }
}
