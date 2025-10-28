<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Order;
use App\Models\Service;
use App\Models\Vacancy;
use App\Services\AuthService;
use App\Services\OrganizationService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected AuthService $authService;
    protected OrganizationService $organizationService;

    public function __construct(AuthService $authService, OrganizationService $organizationService)
    {
        $this->authService = $authService;
        $this->organizationService = $organizationService;
    }

    /**
     * Show the dashboard.
     */
    public function index()
    {
        $manager = $this->authService->getAuthenticatedManager();
        $currentOrganization = $this->organizationService->getCurrentOrganization($manager);
        $organizations = $this->organizationService->getManagerOrganizations($manager);

        if (!$currentOrganization) {
            return view('manager.dashboard', [
                'manager' => $manager,
                'currentOrganization' => null,
                'organizations' => $organizations,
            ]);
        }

        $orgId = $currentOrganization->id;

        // Main Stats
        $stats = [
            'vacancies' => [
                'total' => Vacancy::where('organization_id', $orgId)->count(),
                'active' => Vacancy::where('organization_id', $orgId)->where('is_active', true)->count(),
                'inactive' => Vacancy::where('organization_id', $orgId)->where('is_active', false)->count(),
            ],
            'services' => [
                'total' => Service::where('organization_id', $orgId)->count(),
                'total_value' => Service::where('organization_id', $orgId)->sum('price'),
            ],
            'orders' => [
                'total' => Order::where('organization_id', $orgId)->count(),
                'total_value' => Order::where('organization_id', $orgId)->sum('price'),
                'avg_value' => Order::where('organization_id', $orgId)->avg('price'),
                'pending' => Order::where('organization_id', $orgId)
                    ->whereHas('status', fn($q) => $q->where('name', 'Новый'))
                    ->count(),
                'in_progress' => Order::where('organization_id', $orgId)
                    ->whereHas('status', fn($q) => $q->where('name', 'В работе'))
                    ->count(),
                'completed' => Order::where('organization_id', $orgId)
                    ->whereHas('status', fn($q) => $q->where('name', 'Завершен'))
                    ->count(),
            ],
            'chats' => [
                'total' => Chat::where('organization_id', $orgId)->count(),
                'active_today' => Chat::where('organization_id', $orgId)
                    ->whereHas('messages', fn($q) => $q->whereDate('created_at', today()))
                    ->count(),
                'unread' => Chat::where('organization_id', $orgId)
                    ->whereHas('messages', fn($q) => $q->where('is_read', false)->where('sender_type', 'applicant'))
                    ->count(),
            ],
        ];

        // Orders by Status (for chart)
        $ordersByStatus = Order::where('organization_id', $orgId)
            ->select('status_id', DB::raw('count(*) as count'))
            ->with('status')
            ->groupBy('status_id')
            ->get()
            ->map(fn($item) => [
                'status' => $item->status?->name ?? 'Без статуса',
                'color' => $item->status?->color ?? '#6b7280',
                'count' => $item->count,
            ]);

        // Recent Orders (last 5)
        $recentOrders = Order::where('organization_id', $orgId)
            ->with(['applicant', 'status'])
            ->latest()
            ->take(5)
            ->get();

        // Orders Timeline (last 7 days)
        $ordersTimeline = Order::where('organization_id', $orgId)
            ->where('created_at', '>=', Carbon::now()->subDays(6))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Fill missing dates with 0
        $timeline = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $timeline[$date] = $ordersTimeline[$date] ?? 0;
        }

        // Revenue Timeline (last 7 days)
        $revenueTimeline = Order::where('organization_id', $orgId)
            ->where('created_at', '>=', Carbon::now()->subDays(6))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('sum(price) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        $revenue = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $revenue[$date] = $revenueTimeline[$date] ?? 0;
        }

        // Top Services (by order count)
        $topServices = Service::where('organization_id', $orgId)
            ->withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take(5)
            ->get();

        // Active Chats (with recent messages)
        $activeChats = Chat::where('organization_id', $orgId)
            ->with(['applicant', 'messages' => fn($q) => $q->latest()->take(1)])
            ->whereHas('messages', fn($q) => $q->whereDate('created_at', '>=', Carbon::now()->subDays(7)))
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return view('manager.dashboard', compact(
            'manager',
            'currentOrganization',
            'organizations',
            'stats',
            'ordersByStatus',
            'recentOrders',
            'timeline',
            'revenue',
            'topServices',
            'activeChats'
        ));
    }
}
