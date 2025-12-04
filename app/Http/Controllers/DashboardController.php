<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\Logger;

class DashboardController extends Controller
{
    protected Logger $logger;

    public function __construct()
    {
        $this->logger = Logger::getInstance();
    }

    // =================== Dashboard Main ===================
    public function index()
    {
        $user = auth()->user();
        $this->logDashboardAccess($user);

        if ($user->role === 'admin') {
            $stats = $this->getAdminStats();
            return view('myauth.dashboard', array_merge(['user' => $user], $stats));
        }

        $stats = $this->getUserStats($user);
        return view('myauth.dashboard', array_merge(['user' => $user], $stats));
    }

    // =================== Private Helpers ===================

    private function logDashboardAccess(User $user): void
    {
        $this->safeLog(function() use ($user) {
            $this->logger->info('Dashboard accessed', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ]);
        });
    }

    private function safeLog(callable $callback): void
    {
        try {
            $callback();
        } catch (\Exception $e) {
            $this->logger->error('Logging failed', ['error' => $e->getMessage()]);
        }
    }

    private function getAdminStats(): array
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

    private function getUserStats(User $user): array
    {
        $properties = $user->properties;

        return [
            'listings' => $properties->count(),
            'reservations' => $properties->where('status','reserved')->count(),
            'visitors' => $user->visitors ?? 0,
            'pieData' => $this->generatePieData($properties),
            'salesData' => $this->generateMonthlyStats($properties),
        ];
    }

    private function calculateDelta(int $current, int $previous): int
    {
        return $previous === 0 ? 0 : round((($current - $previous) / $previous) * 100);
    }

    // =================== Admin AJAX Endpoint ===================
public function getClientData(Request $request)
{
    $user = auth()->user();

    if ($user->role !== 'admin') {
        $this->safeLog(fn() => $this->logger->warning('Unauthorized client data access', [
            'user_id' => $user->id,
            'email' => $user->email
        ]));
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    try {
        if (empty($request->id)) {
            $properties = Property::all();
            $visitors = DB::table('sessions')->count();
        } else {
            $client = User::with('properties')->find($request->id);
            if (!$client) {
                $this->logger->warning('Client not found', ['client_id' => $request->id]);
                return response()->json(['error' => 'Client not found'], 404);
            }
            $properties = $client->properties;
            $visitors = $client->visitors ?? 0;
        }

        return response()->json([
            'listings' => $properties->count(),
            'reservations' => $properties->where('status', 'reserved')->count(),
            'visitors' => $visitors,
            'sales' => $this->generateMonthlyStats($properties),
            'pie' => $this->generatePieData($properties), // ✅ key matches JS
        ]);

    } catch (\Exception $e) {
        $this->logger->error('Failed to get client data', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Failed to retrieve data'], 500);
    }
}


    // =================== Stats Helpers ===================
    private function generateMonthlyStats($properties): array
    {
        $sales = [];
        $now = Carbon::now();

        for ($i = 11; $i >= 0; $i--) {
            $start = $now->copy()->startOfMonth()->subMonths($i);
            $end = $start->copy()->endOfMonth();

            $monthlyProperties = $properties->filter(fn($p) => $p->created_at >= $start && $p->created_at <= $end);
            $monthlyReservations = $monthlyProperties->where('status','reserved')->count();

            $sales[] = [
                'label' => $start->format('M'),
                'listings' => $monthlyProperties->count(),
                'reservations' => $monthlyReservations,
                'visitors' => rand(50,150) // Replace with real data if needed
            ];
        }

        return $sales;
    }

    private function generatePieData($properties): array
    {
        return $properties->groupBy('category')->map->count()->toArray();
    }
}
