<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessController extends Controller
{
    /**
     * Display a listing of businesses.
     */
    public function index()
    {
        $search = request("search", "");
        $sort = request("sort", "created_at");
        $direction = request("direction", "desc");
        $perPage = min((int) request("per_page", 20), 100);

        // ОПТИМИЗИРОВАНО: Подзапросы вместо withCount
        $ownerRoleId = BusinessRole::where("slug", "owner")->value("id");
        $query = Business::query()->with([
            "users" => function ($q) use ($ownerRoleId) {
                $q->wherePivotIn("role_id", [$ownerRoleId]);
            },
        ])->selectRaw('businesses.*,
                (SELECT COUNT(*) FROM clients WHERE clients.business_id = businesses.id) as clients_count,
                (SELECT COUNT(*) FROM services WHERE services.business_id = businesses.id) as services_count,
                (SELECT COUNT(*) FROM masters WHERE masters.business_id = businesses.id) as masters_count,
                (SELECT COUNT(*) FROM locations WHERE locations.business_id = businesses.id) as locations_count,
                (SELECT COUNT(*) FROM appointments WHERE appointments.business_id = businesses.id) as appointments_count');

        // ОПТИМИЗИРОВАННЫЙ ПОИСК
        if ($search) {
            $query
                ->where(function ($q) use ($search) {
                    $q->where("name", "like", "%{$search}%")
                        ->orWhere("description", "like", "%{$search}%")
                        // Поиск по телефону через подзапрос (быстрее whereHas)
                        ->orWhereIn("id", function ($subquery) use ($search) {
                            $subquery
                                ->select("phoneable_id")
                                ->from("phones")
                                ->where("phoneable_type", Business::class)
                                ->where("phone", "like", "%{$search}%");
                        });
                })
                ->limit(1000); // Ограничение для поиска
        }

        // Сортировка
        $allowedSorts = ["name", "created_at"];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy("created_at", "desc");
        }

        // ОПТИМИЗАЦИЯ: simplePaginate вместо paginate
        $businesses = $query->simplePaginate($perPage)->withQueryString();

        return view(
            "panel.businesses.index",
            compact("businesses", "search", "sort", "direction", "perPage"),
        );
    }

    /**
     * Display the specified business.
     */
    public function show(Business $business)
    {
        // КРИТИЧЕСКАЯ ОПТИМИЗАЦИЯ: НЕ загружаем все связанные записи!
        // Загружаем только owners и counts
        $ownerRoleId = BusinessRole::where("slug", "owner")->value("id");
        $business->load([
            "users" => function ($q) use ($ownerRoleId) {
                $q->wherePivotIn("role_id", [$ownerRoleId]);
            },
        ]);

        // Используем selectRaw для одного запроса вместо 5
        $counts = DB::selectOne(
            '
            SELECT
                (SELECT COUNT(*) FROM clients WHERE clients.business_id = ?) as clients_count,
                (SELECT COUNT(*) FROM services WHERE services.business_id = ?) as services_count,
                (SELECT COUNT(*) FROM masters WHERE masters.business_id = ?) as masters_count,
                (SELECT COUNT(*) FROM locations WHERE locations.business_id = ?) as locations_count,
                (SELECT COUNT(*) FROM appointments WHERE appointments.business_id = ?) as appointments_count
        ',
            [
                $business->id,
                $business->id,
                $business->id,
                $business->id,
                $business->id,
            ],
        );

        // Добавляем counts как атрибуты
        $business->clients_count = $counts->clients_count;
        $business->services_count = $counts->services_count;
        $business->masters_count = $counts->masters_count;
        $business->locations_count = $counts->locations_count;
        $business->appointments_count = $counts->appointments_count;

        return view("panel.businesses.show", compact("business"));
    }

    /**
     * Show the form for editing the specified business.
     */
    public function edit(Business $business)
    {
        return view("panel.businesses.edit", compact("business"));
    }

    /**
     * Update the specified business in storage.
     */
    public function update(Request $request, Business $business)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "phone" => "nullable|string|max:20",
            "description" => "nullable|string|max:500",
        ]);

        $business->update($validated);

        return redirect()
            ->route("panel.businesses.show", $business)
            ->with("success", "Бизнес успешно обновлен");
    }

    /**
     * Remove the specified business from storage.
     */
    public function destroy(Business $business)
    {
        // ОПТИМИЗАЦИЯ: exists() вместо count() (быстрее!)
        if (
            $business->clients()->exists() ||
            $business->appointments()->exists() ||
            $business->services()->exists() ||
            $business->masters()->exists() ||
            $business->locations()->exists()
        ) {
            return redirect()
                ->route("panel.businesses.show", $business)
                ->with(
                    "error",
                    "Невозможно удалить бизнес, так как у него есть связанные данные (клиенты, записи, услуги, мастера или локации)",
                );
        }

        \App\Services\AdminNotificationService::notifyBusinessDeleted(
            $business,
            request()->user(),
        );

        $business->delete();

        return redirect()
            ->route("panel.businesses")
            ->with("success", "Бизнес успешно удален");
    }
}
