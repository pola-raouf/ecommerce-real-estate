<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\DashboardService;
use App\Services\Logger;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;
    protected Logger $logger;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
        $this->logger = Logger::getInstance();
        $this->middleware('auth');
    }

    /**
     * Display the dashboard
     */
    public function index()
    {
        $user = auth()->user();
        $this->logDashboardAccess($user);

        try {
            if ($user->role === 'admin') {
                $stats = $this->dashboardService->getAdminStats();
            } else {
                $stats = $this->dashboardService->getUserStats($user);
            }

            return view('myauth.dashboard', array_merge(['user' => $user], $stats));
        } catch (\Exception $e) {
            $this->logger->error('Dashboard loading failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to load dashboard data.');
        }
    }

    /**
     * Get client data for admin dashboard (AJAX)
     */
    public function getClientData(Request $request)
    {
        $user = auth()->user();

        // Authorization check
        if ($user->role !== 'admin') {
            $this->logger->warning('Unauthorized client data access', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $clientId = $request->input('id');
            $data = $this->dashboardService->getClientData($clientId);

            return response()->json($data);
        } catch (\Exception $e) {
            $this->logger->error('Failed to get client data', [
                'client_id' => $request->input('id'),
                'error' => $e->getMessage()
            ]);

            $statusCode = $e->getMessage() === 'Client not found' ? 404 : 500;
            return response()->json(['error' => $e->getMessage()], $statusCode);
        }
    }

    /**
     * Log dashboard access
     */
    private function logDashboardAccess(User $user): void
    {
        try {
            $this->logger->info('Dashboard accessed', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ]);
        } catch (\Exception $e) {
            // Silent fail - don't break dashboard if logging fails
            $this->logger->error('Logging failed', ['error' => $e->getMessage()]);
        }
    }
}
