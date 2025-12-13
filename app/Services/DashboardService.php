<?php

namespace App\Services;

use App\Models\Property;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    protected Logger $logger;

    public function __construct()
    {
        $this->logger = Logger::getInstance();
    }

    /**
     * Get admin dashboard statistics
     */
    public function getAdminStats(): array
    {
        $properties = Property::all();
        $clients = User::where('role', 'seller')->get();

        $totalListings = Property::count();
        $listingsDelta = $this->calculateDelta(
            Property::where('created_at', '>=', now()->subDays(30))->count(),
            Property::whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])->count()
        );

        $totalReservations = Property::where('status', 'reserved')->count();
        $reservationsDelta = $this->calculateDelta(
            Property::where('status', 'reserved')->where('updated_at', '>=', now()->subDays(30))->count(),
            Property::where('status', 'reserved')->whereBetween('updated_at', [now()->subDays(60), now()->subDays(30)])->count()
        );

        $totalVisitors = DB::table('sessions')->count();
        $visitorsDelta = $this->calculateDelta(
            DB::table('sessions')->where('last_activity', '>=', now()->subDays(30)->timestamp)->count(),
            DB::table('sessions')->whereBetween('last_activity', [now()->subDays(60)->timestamp, now()->subDays(30)->timestamp])->count()
        );

        return [
            'totalListings' => $totalListings,
            'listingsDelta' => $listingsDelta,
            'totalReservations' => $totalReservations,
            'reservationsDelta' => $reservationsDelta,
            'totalVisitors' => $totalVisitors,
            'visitorsDelta' => $visitorsDelta,
            'clients' => $clients,
            'pieData' => $this->generatePieData($properties),
            'salesData' => $this->generateMonthlyStats($properties),
        ];
    }

    /**
     * Get user (seller) dashboard statistics
     */
    public function getUserStats(User $user): array
    {
        $properties = $user->properties;

        return [
            'listings' => $properties->count(),
            'reservations' => $properties->where('status', 'reserved')->count(),
            'visitors' => $user->visitors ?? 0,
            'pieData' => $this->generatePieData($properties),
            'salesData' => $this->generateMonthlyStats($properties),
        ];
    }

    /**
     * Get client data for admin dashboard (AJAX)
     */
    public function getClientData(?int $clientId = null): array
    {
        if (empty($clientId)) {
            // All properties
            $properties = Property::all();
            $visitors = DB::table('sessions')->count();
        } else {
            // Specific client
            $client = User::with('properties')->find($clientId);
            
            if (!$client) {
                $this->logger->warning('Client not found', ['client_id' => $clientId]);
                throw new \Exception('Client not found');
            }
            
            $properties = $client->properties;
            $visitors = $client->visitors ?? 0;
        }

        return [
            'listings' => $properties->count(),
            'reservations' => $properties->where('status', 'reserved')->count(),
            'visitors' => $visitors,
            'sales' => $this->generateMonthlyStats($properties),
            'pie' => $this->generatePieData($properties),
        ];
    }

    /**
     * Calculate percentage delta between current and previous period
     */
    private function calculateDelta(int $current, int $previous): int
    {
        return $previous === 0 ? 0 : round((($current - $previous) / $previous) * 100);
    }

    /**
     * Generate monthly statistics for the last 12 months
     */
    private function generateMonthlyStats($properties): array
    {
        $sales = [];
        $now = Carbon::now();

        for ($i = 11; $i >= 0; $i--) {
            $start = $now->copy()->startOfMonth()->subMonths($i);
            $end = $start->copy()->endOfMonth();

            $monthlyProperties = $properties->filter(fn($p) => $p->created_at >= $start && $p->created_at <= $end);
            $monthlyReservations = $monthlyProperties->where('status', 'reserved')->count();

            $sales[] = [
                'label' => $start->format('M'),
                'listings' => $monthlyProperties->count(),
                'reservations' => $monthlyReservations,
                'visitors' => rand(50, 150) // TODO: Replace with real visitor data
            ];
        }

        return $sales;
    }

    /**
     * Generate pie chart data grouped by category
     */
    private function generatePieData($properties): array
    {
        return $properties->groupBy('category')->map->count()->toArray();
    }
}
