<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $summary = [
            'projects' => Project::count(),
            'published' => Project::where('is_published', true)->count(),
            'orders' => Order::count(),
            'new_orders' => Order::where('status', 'new')->count(),
        ];
        $statusLabels = ['new' => 'Baru', 'contacted' => 'Dihubungi', 'discussion' => 'Diskusi', 'in_progress' => 'Dikerjakan', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
        $statusCounts = Order::selectRaw('status, COUNT(*) total')->groupBy('status')->pluck('total', 'status');
        $orderStatusChart = ['labels' => array_values($statusLabels), 'series' => array_map(fn ($status) => (int) ($statusCounts[$status] ?? 0), array_keys($statusLabels))];

        $start = now()->startOfMonth()->subMonths(5);
        $monthExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";
        $monthlyRaw = Order::selectRaw("{$monthExpression} period, COUNT(*) total")
            ->where('created_at', '>=', $start)->groupBy('period')->pluck('total', 'period');
        $labels = [];
        $series = [];
        for ($cursor = $start->copy(), $i = 0; $i < 6; $i++, $cursor->addMonth()) {
            $labels[] = $cursor->translatedFormat('M Y');
            $series[] = (int) ($monthlyRaw[$cursor->format('Y-m')] ?? 0);
        }
        $orderTrendChart = compact('labels', 'series');
        $recentOrders = Order::latest()->limit(7)->get();
        $recentProjects = Project::latest()->limit(5)->get();

        return view('admin.index', compact('summary', 'orderStatusChart', 'orderTrendChart', 'recentOrders', 'recentProjects', 'statusLabels'));
    }
}
