<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\NotificationBroadcast;
use App\Services\BroadcastNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BroadcastController extends Controller
{
    public function index()
    {
        $this->authorize('panel.broadcasts.send');

        $broadcasts = NotificationBroadcast::with('sentBy')
            ->orderBy('sent_at', 'desc')
            ->paginate(15);

        return view('panel.broadcasts.index', compact('broadcasts'));
    }

    public function create()
    {
        $this->authorize('panel.broadcasts.send');

        return view('panel.broadcasts.create');
    }

    public function store(Request $request)
    {
        $this->authorize('panel.broadcasts.send');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:10000',
            'target' => 'required|in:owners,all',
            'channels' => 'required|array|min:1',
            'channels.*' => 'in:system,email,telegram',
        ]);

        BroadcastNotificationService::send(
            $validated['title'],
            $validated['message'],
            $validated['target'],
            array_values(array_unique($validated['channels'])),
            Auth::user()
        );

        return redirect()
            ->route('panel.broadcasts.index')
            ->with('success', 'Рассылка поставлена в очередь. Уведомления будут разосланы в ближайшее время.');
    }
}
