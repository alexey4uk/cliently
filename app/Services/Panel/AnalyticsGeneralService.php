<?php

namespace App\Services\Panel;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\Client;
use App\Models\Master;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsGeneralService
{
    public function getGeneralData(array $filters): array
    {
        $startDate = Carbon::parse($filters['date_from'])->startOfDay();
        $endDate = Carbon::parse($filters['date_to'])->endOfDay();

        $query = Appointment::whereBetween('date', [
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d'),
        ]);
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        $appointmentsStats = $query
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statsByStatus = [
            'pending' => $appointmentsStats['pending'] ?? 0,
            'confirmed' => $appointmentsStats['confirmed'] ?? 0,
            'completed' => $appointmentsStats['completed'] ?? 0,
            'cancelled' => $appointmentsStats['cancelled'] ?? 0,
        ];

        $total = array_sum($appointmentsStats);
        $completed = $statsByStatus['completed'];
        $cancelled = $statsByStatus['cancelled'];
        $conversionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
        $cancellationRate = $total > 0 ? round(($cancelled / $total) * 100, 1) : 0;

        $totalBusinesses = Business::count();
        try {
            $appointmentsInfo = DB::selectOne(
                "SELECT table_rows FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'appointments'",
            );
            $totalAppointmentsApprox = $appointmentsInfo && isset($appointmentsInfo->table_rows)
                ? (int) $appointmentsInfo->table_rows
                : $total;
        } catch (\Exception $e) {
            $totalAppointmentsApprox = $total;
        }

        $avgAppointmentsPerBusiness = $totalBusinesses > 0
            ? round($totalAppointmentsApprox / $totalBusinesses, 1)
            : 0;
        $avgClientsPerBusiness = $totalBusinesses > 0
            ? round(Client::count() / $totalBusinesses, 1)
            : 0;

        $appointmentsByPeriod = $this->getAppointmentsByPeriod($startDate, $endDate, $filters);

        $statsByBusiness = DB::table('businesses')
            ->leftJoin('appointments', function ($join) use ($startDate, $endDate) {
                $join->on('businesses.id', '=', 'appointments.business_id')
                    ->whereBetween('appointments.date', [
                        $startDate->format('Y-m-d'),
                        $endDate->format('Y-m-d'),
                    ]);
            })
            ->select(
                'businesses.id',
                'businesses.name',
                DB::raw('COUNT(appointments.id) as appointments_count'),
            )
            ->groupBy('businesses.id', 'businesses.name')
            ->orderBy('appointments_count', 'DESC')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'count' => (int) $item->appointments_count,
                ];
            });

        $serviceStats = Appointment::whereBetween('date', [
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d'),
        ])
            ->select('service_id', DB::raw('COUNT(*) as count'))
            ->groupBy('service_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        $serviceIds = $serviceStats->pluck('service_id');
        $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');
        $statsByService = $serviceStats->map(function ($item) use ($services) {
            return [
                'service_id' => $item->service_id,
                'service_name' => $services[$item->service_id]->name ?? 'Неизвестная услуга',
                'count' => $item->count,
            ];
        });

        $masterStats = Appointment::whereBetween('date', [
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d'),
        ])
            ->select('master_id', DB::raw('COUNT(*) as count'))
            ->groupBy('master_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        $masterIds = $masterStats->pluck('master_id');
        $masters = Master::whereIn('id', $masterIds)->get()->keyBy('id');
        $statsByMaster = $masterStats->map(function ($item) use ($masters) {
            $master = $masters[$item->master_id] ?? null;
            $masterName = $master
                ? trim($master->first_name.' '.($master->last_name ?? ''))
                : 'Неизвестный мастер';

            return [
                'master_id' => $item->master_id,
                'master_name' => $masterName,
                'count' => $item->count,
            ];
        });

        return [
            'total' => $total,
            'stats_by_status' => $statsByStatus,
            'conversion_rate' => $conversionRate,
            'cancellation_rate' => $cancellationRate,
            'avg_appointments_per_business' => $avgAppointmentsPerBusiness,
            'avg_clients_per_business' => $avgClientsPerBusiness,
            'appointments_by_period' => $appointmentsByPeriod,
            'stats_by_business' => $statsByBusiness,
            'stats_by_service' => $statsByService,
            'stats_by_master' => $statsByMaster,
        ];
    }

    public function getAppointmentsByPeriod(Carbon $startDate, Carbon $endDate, array $filters): array
    {
        $query = Appointment::whereBetween('date', [
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d'),
        ]);
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        $appointmentsByDate = $query
            ->selectRaw(
                'date,
                COUNT(*) as total,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled',
            )
            ->groupBy('date')
            ->get()
            ->keyBy(fn ($item) => $item->date);

        $appointmentsByDay = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayStats = $appointmentsByDate->get($dateStr);
            $appointmentsByDay[] = [
                'date' => $dateStr,
                'label' => $currentDate->format('d.m'),
                'total' => $dayStats ? (int) $dayStats->total : 0,
                'completed' => $dayStats ? (int) $dayStats->completed : 0,
                'cancelled' => $dayStats ? (int) $dayStats->cancelled : 0,
            ];
            $currentDate->addDay();
        }

        return $appointmentsByDay;
    }
}
