<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        $subscriptionService = app(SubscriptionService::class);

        if (! $subscriptionService->checkLimit($user, $feature)) {
            return redirect()->back()
                ->with('error', 'Достигнут лимит для вашего тарифа. Обновите тариф для увеличения лимита.');
        }

        return $next($request);
    }
}
