<?php

namespace App\Services\Analytics;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsClientsService
{
    public function getClientsAnalyticsData(int $businessId, array $filters): array
    {
        $startDate = Carbon::parse($filters['date_from'])->startOfDay();
        $endDate = Carbon::parse($filters['date_to'])->endOfDay();

        $appointmentsInPeriod = Appointment::where('business_id', $businessId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->with('client')
            ->get();

        $clientIdsInPeriod = $appointmentsInPeriod->pluck('client_id')->unique();

        $firstAppointments = Appointment::where('business_id', $businessId)
            ->whereIn('client_id', $clientIdsInPeriod)
            ->select('client_id', DB::raw('MIN(date) as first_date'))
            ->groupBy('client_id')
            ->get()
            ->keyBy('client_id');

        $newClients = [];
        $returningClients = [];
        foreach ($clientIdsInPeriod as $clientId) {
            $first = $firstAppointments->get($clientId);
            if ($first && $first->first_date >= $startDate->format('Y-m-d')) {
                $newClients[] = $clientId;
            } else {
                $returningClients[] = $clientId;
            }
        }

        $completedAppointments = Appointment::where('business_id', $businessId)
            ->where('status', 'completed')
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->with(['client', 'service'])
            ->get();

        $clientsLTV = $completedAppointments->groupBy('client_id')->map(function ($group, $clientId) {
            $client = $group->first()->client;
            return [
                'client_id' => $clientId,
                'client_name' => $client ? $client->full_name : 'Неизвестный клиент',
                'ltv' => $group->sum(fn ($a) => $a->price ?? $a->service?->price ?? 0),
                'appointments_count' => $group->count(),
            ];
        })->sortByDesc('ltv')->take(10)->values();

        $averageLTV = $clientsLTV->count() > 0 ? round($clientsLTV->avg('ltv'), 2) : 0;
        $uniqueCount = $clientIdsInPeriod->count();
        $visitFrequency = $uniqueCount > 0 ? round($appointmentsInPeriod->count() / $uniqueCount, 2) : 0;
        $newClientsByPeriod = $this->getNewClientsByPeriod($businessId, $startDate, $endDate);

        return [
            'new_clients' => count($newClients),
            'returning_clients' => count($returningClients),
            'total_clients' => $uniqueCount,
            'average_ltv' => $averageLTV,
            'top_clients' => $clientsLTV->toArray(),
            'visit_frequency' => $visitFrequency,
            'new_clients_by_period' => $newClientsByPeriod,
        ];
    }

    private function getNewClientsByPeriod(int $businessId, Carbon $startDate, Carbon $endDate): array
    {
        $allAppointments = Appointment::where('business_id', $businessId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date')->orderBy('time')->get();

        $firstByClient = [];
        foreach ($allAppointments as $a) {
            if (!isset($firstByClient[$a->client_id])) {
                $firstByClient[$a->client_id] = $a->date->format('Y-m-d');
            }
        }

        $byDate = $allAppointments->groupBy(fn ($a) => $a->date->format('Y-m-d'));
        $result = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $dateStr = $current->format('Y-m-d');
            $dayAppointments = $byDate->get($dateStr, collect());
            $newOnDay = $dayAppointments->filter(function ($a) use ($firstByClient, $dateStr) {
                return isset($firstByClient[$a->client_id]) && $firstByClient[$a->client_id] === $dateStr;
            })->pluck('client_id')->unique()->count();
            $result[] = ['date' => $dateStr, 'label' => $current->format('d.m'), 'count' => $newOnDay];
            $current->addDay();
        }
        return $result;
    }
}
