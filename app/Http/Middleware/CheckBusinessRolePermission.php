<?php

namespace App\Http\Middleware;

use App\Services\BusinessRolePermissionService;
use App\Traits\HasCurrentBusiness;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBusinessRolePermission
{
    use HasCurrentBusiness;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $role = $this->getCurrentBusinessRole();

        if (!$role) {
            abort(403, 'У вас нет роли в этом бизнесе.');
        }

        $service = app(BusinessRolePermissionService::class);

        if (!$service->hasPermission($business->id, $role, $permission)) {
            abort(403, 'У вас нет прав для выполнения этого действия.');
        }

        return $next($request);
    }
}
