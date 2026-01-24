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
    public function handle(Request $request, Closure $next, ...$permissionParts): Response
    {
        // Laravel splits middleware parameters by dots, so we need to reconstruct the full permission name
        // Join all parts back together with dots
        $permission = implode('.', $permissionParts);
        
        // If permission doesn't start with 'client.' or 'panel.', prepend 'client.' as default
        // This handles cases where Laravel strips the prefix when parsing middleware parameters
        if (!str_starts_with($permission, 'client.') && !str_starts_with($permission, 'panel.')) {
            $permission = 'client.' . $permission;
        }

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

        if (!$service->hasPermission($role->id, $permission)) {
            abort(403, 'У вас нет прав для выполнения этого действия.');
        }

        return $next($request);
    }
}
