<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(7)->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $period = $request->get('period', 'weekly');

        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        if ($endDate->lt($startDate)) {
            $endDate = $startDate->copy()->addDays(7);
        }

        \Log::info('Statistics Debug:', [
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'period' => $period,
            'current_week_start' => Carbon::now()->startOfWeek()->toDateString(),
            'current_week_end' => Carbon::now()->endOfWeek()->toDateString()
        ]);

        $stats = $this->getOverviewStatsWithFilter($startDate, $endDate, $period);
        $chartData = $this->getChartDataWithFilter($startDate, $endDate, $period);
        $topProducts = $this->getTopProductsWithFilter($startDate, $endDate);
        $orderStats = $this->getOrderStatsWithFilter($startDate, $endDate);
        $weeklyStats = $this->getTimeSeriesStatsWithFilter($startDate, $endDate, $period);
        $categoryStats = $this->getCategoryStatsWithFilter($startDate, $endDate);

        return view('admin.statistics.index', compact(
            'stats', 'chartData', 'topProducts', 'orderStats',
            'weeklyStats', 'categoryStats', 'startDate', 'endDate', 'period'
        ));
    }

    private function getOverviewStatsWithFilter($startDate, $endDate, $period = 'weekly')
    {
        if ($period == 'weekly') {
            $currentWeekStart = Carbon::now()->startOfWeek();
            $currentWeekEnd = Carbon::now()->endOfWeek();

            $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
            $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();

            $currentRevenue = DB::table('orders')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
                ->sum('total_amount') ?? 0;

            $currentOrders = DB::table('orders')
                ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
                ->count() ?? 0;

            $previousRevenue = DB::table('orders')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
                ->sum('total_amount') ?? 0;

            $previousOrders = DB::table('orders')
                ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
                ->count() ?? 0;

            $newCustomers = DB::table('customers')
                ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
                ->count() ?? 0;


            \Log::info('Weekly Stats Debug:', [
                'currentWeekStart' => $currentWeekStart->toDateTimeString(),
                'currentWeekEnd' => $currentWeekEnd->toDateTimeString(),
                'currentRevenue' => $currentRevenue,
                'currentOrders' => $currentOrders,
                'previousRevenue' => $previousRevenue,
                'previousOrders' => $previousOrders,
                'newCustomers' => $newCustomers
            ]);

        } else {
            $currentRevenue = DB::table('orders')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total_amount') ?? 0;

            $currentOrders = DB::table('orders')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count() ?? 0;

            $daysDiff = $startDate->diffInDays($endDate) + 1;
            $previousStart = $startDate->copy()->subDays($daysDiff);
            $previousEnd = $startDate->copy()->subDay();

            $previousRevenue = DB::table('orders')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$previousStart, $previousEnd])
                ->sum('total_amount') ?? 0;

            $previousOrders = DB::table('orders')
                ->whereBetween('created_at', [$previousStart, $previousEnd])
                ->count() ?? 0;

            $newCustomers = DB::table('customers')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count() ?? 0;
        }

        $revenueGrowth = $previousRevenue > 0 ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;
        $ordersGrowth = $previousOrders > 0 ? (($currentOrders - $previousOrders) / $previousOrders) * 100 : 0;

        return [
            'revenue' => [
                'current' => $currentRevenue,
                'growth' => round($revenueGrowth, 1)
            ],
            'orders' => [
                'current' => $currentOrders,
                'growth' => round($ordersGrowth, 1)
            ],
            'products' => $this->getProductCount(),
            'customers' => [
                'total' => $this->getCustomerCount(),
                'new_this_week' => $newCustomers,
                'new_today' => $this->getNewCustomersToday()
            ]
        ];
    }

    private function getChartDataWithFilter($startDate, $endDate, $period)
    {
        $labels = [];
        $data = [];

        if ($period == 'daily') {
            $daysDiff = $startDate->diffInDays($endDate);
            $daysDiff = min($daysDiff, 30);

            for ($i = 0; $i <= $daysDiff; $i++) {
                $date = $startDate->copy()->addDays($i);

                $revenue = DB::table('orders')
                    ->where('status', 'completed')
                    ->whereDate('created_at', $date->toDateString())
                    ->sum('total_amount') ?? 0;

                $labels[] = $date->format('d/m');
                $data[] = (float) $revenue;
            }
        } else {
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);

                $revenue = DB::table('orders')
                    ->where('status', 'completed')
                    ->whereDate('created_at', $date->toDateString())
                    ->sum('total_amount') ?? 0;

                $labels[] = $date->format('d/m');
                $data[] = (float) $revenue;

                \Log::info("Chart Data for {$date->format('d/m')}: {$revenue}");
            }
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }


    private function getTimeSeriesStatsWithFilter($startDate, $endDate, $period)
    {
        $timeSeriesData = [];

        if ($period == 'daily') {
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);

                $revenue = DB::table('orders')
                    ->where('status', 'completed')
                    ->whereDate('created_at', $date->toDateString())
                    ->sum('total_amount') ?? 0;

                $orders = DB::table('orders')
                    ->whereDate('created_at', $date->toDateString())
                    ->count() ?? 0;

                $newCustomers = DB::table('customers')
                    ->whereDate('created_at', $date->toDateString())
                    ->count() ?? 0;

                $productsSold = 0;
                try {
                    $productsSold = DB::table('order_items')
                        ->join('orders', 'order_items.order_id', '=', 'orders.id')
                        ->where('orders.status', 'completed')
                        ->whereDate('orders.created_at', $date->toDateString())
                        ->sum('order_items.quantity') ?? 0;
                } catch (\Exception $e) {
                    $productsSold = 0;
                }

                $previousDate = $date->copy()->subDay();
                $previousRevenue = DB::table('orders')
                    ->where('status', 'completed')
                    ->whereDate('created_at', $previousDate->toDateString())
                    ->sum('total_amount') ?? 0;

                $growth = $previousRevenue > 0 ? (($revenue - $previousRevenue) / $previousRevenue) * 100 : 0;

                $timeSeriesData[] = [
                    'week' => $date->format('d/m (D)'),
                    'revenue' => $revenue,
                    'orders' => $orders,
                    'products_sold' => $productsSold,
                    'new_customers' => $newCustomers,
                    'growth' => round($growth, 1)
                ];
            }
        } else {
            for ($i = 3; $i >= 0; $i--) {
                $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
                $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();

                $revenue = DB::table('orders')
                    ->where('status', 'completed')
                    ->whereBetween('created_at', [$weekStart, $weekEnd])
                    ->sum('total_amount') ?? 0;

                $orders = DB::table('orders')
                    ->whereBetween('created_at', [$weekStart, $weekEnd])
                    ->count() ?? 0;

                $newCustomers = DB::table('customers')
                    ->whereBetween('created_at', [$weekStart, $weekEnd])
                    ->count() ?? 0;

                $productsSold = 0;
                try {
                    $productsSold = DB::table('order_items')
                        ->join('orders', 'order_items.order_id', '=', 'orders.id')
                        ->where('orders.status', 'completed')
                        ->whereBetween('orders.created_at', [$weekStart, $weekEnd])
                        ->sum('order_items.quantity') ?? 0;
                } catch (\Exception $e) {
                    $productsSold = 0;
                }

                $lastWeekStart = $weekStart->copy()->subWeek();
                $lastWeekEnd = $weekEnd->copy()->subWeek();
                $lastRevenue = DB::table('orders')
                    ->where('status', 'completed')
                    ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
                    ->sum('total_amount') ?? 0;

                $growth = $lastRevenue > 0 ? (($revenue - $lastRevenue) / $lastRevenue) * 100 : 0;

                $timeSeriesData[] = [
                    'week' => 'Tuần ' . $weekStart->format('d/m') . ' - ' . $weekEnd->format('d/m'),
                    'revenue' => $revenue,
                    'orders' => $orders,
                    'products_sold' => $productsSold,
                    'new_customers' => $newCustomers,
                    'growth' => round($growth, 1)
                ];

                \Log::info("Week {$weekStart->format('d/m')} - {$weekEnd->format('d/m')}: Revenue={$revenue}, Orders={$orders}");
            }
        }

        return $timeSeriesData;
    }

    private function getTopProductsWithFilter($startDate, $endDate)
    {
        try {
            return DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.status', 'completed')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->select(
                    'products.name',
                    'products.description',
                    DB::raw('SUM(order_items.quantity) as total_sold')
                )
                ->groupBy('products.id', 'products.name', 'products.description')
                ->orderBy('total_sold', 'desc')
                ->limit(4)
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    private function getOrderStatsWithFilter($startDate, $endDate)
    {
        $statuses = ['completed', 'processing', 'shipping', 'cancelled'];
        $stats = [];

        foreach ($statuses as $status) {
            $count = DB::table('orders')
                ->where('status', $status)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count() ?? 0;

            $stats[$status] = $count;
        }

        return $stats;
    }

    private function getCategoryStatsWithFilter($startDate, $endDate)
    {
        try {
            return DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.status', 'completed')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->select(
                    'categories.name',
                    DB::raw('SUM(order_items.quantity * order_items.price) as revenue')
                )
                ->groupBy('categories.id', 'categories.name')
                ->orderBy('revenue', 'desc')
                ->limit(4)
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    private function getProductCount()
    {
        try {
            return DB::table('products')->where('status', true)->count() ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getCustomerCount()
    {
        try {
            return DB::table('customers')->count() ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getNewCustomersToday()
    {
        try {
            return DB::table('customers')
                ->whereDate('created_at', Carbon::today())
                ->count() ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function apiData(Request $request)
    {
        $startDate = Carbon::parse($request->get('start_date', Carbon::now()->subDays(7)->toDateString()));
        $endDate = Carbon::parse($request->get('end_date', Carbon::now()->toDateString()));
        $period = $request->get('period', 'weekly');

        return response()->json([
            'stats' => $this->getOverviewStatsWithFilter($startDate, $endDate, $period),
            'chartData' => $this->getChartDataWithFilter($startDate, $endDate, $period),
            'topProducts' => $this->getTopProductsWithFilter($startDate, $endDate),
            'orderStats' => $this->getOrderStatsWithFilter($startDate, $endDate),
            'timeSeriesStats' => $this->getTimeSeriesStatsWithFilter($startDate, $endDate, $period),
            'categoryStats' => $this->getCategoryStatsWithFilter($startDate, $endDate)
        ]);
    }
}
