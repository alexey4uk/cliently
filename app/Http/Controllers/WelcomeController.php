<?php

namespace App\Http\Controllers;

use App\Models\BusinessUserInvitation;
use App\Traits\HasCurrentBusiness;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    use HasCurrentBusiness;

    /**
     * Display the welcome/onboarding page.
     */
    public function index()
    {
        $user = Auth::user();
        $business = $this->getCurrentBusiness();

        // Если у пользователя уже есть бизнес, перенаправляем на dashboard
        if ($business) {
            return redirect()->route('dashboard');
        }

        // Получаем активные приглашения для пользователя
        $invitations = BusinessUserInvitation::where('email', $user->email)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->with(['business', 'creator', 'businessRole'])
            ->get();

        return view('onboarding', [
            'invitations' => $invitations,
        ]);
    }
}
