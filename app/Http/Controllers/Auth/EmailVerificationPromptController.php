<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RedirectsAfterAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    use RedirectsAfterAuth;

    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended($this->getRedirectAfterAuth($user));
        }

        return view('auth.verify-email');
    }
}
