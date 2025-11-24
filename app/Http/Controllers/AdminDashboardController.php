<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\CustomerOrder;
use App\Models\MarketplaceListing;
use App\Models\RiskPredictionLog;
use App\Models\ForumThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Key Metrics & Growth
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        // Users
        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();
        $newUsersLastMonth = User::whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->count();
        $userGrowth = $newUsersLastMonth > 0 ? (($newUsersThisMonth - $newUsersLastMonth) / $newUsersLastMonth) * 100 : 100;

        // Revenue (From Customer Orders - Platform Fee)
        $totalRevenue = CustomerOrder::sum('platform_fee');
        $revenueThisMonth = CustomerOrder::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->sum('platform_fee');
        $revenueLastMonth = CustomerOrder::whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->sum('platform_fee');
        $revenueGrowth = $revenueLastMonth > 0 ? (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100 : 100;

        // Listings
        $activeListings = MarketplaceListing::where('status', 'approved')->count();
        $totalListings = MarketplaceListing::count();
        
        // Predictions
        $totalPredictions = RiskPredictionLog::count();
        $predictionsThisMonth = RiskPredictionLog::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();
        $predictionsLastMonth = RiskPredictionLog::whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->count();
        $predictionGrowth = $predictionsLastMonth > 0 ? (($predictionsThisMonth - $predictionsLastMonth) / $predictionsLastMonth) * 100 : 100;

        // 2. Revenue Chart Data (Last 30 Days)
        $dailyRevenue = CustomerOrder::selectRaw('DATE(created_at) as date, SUM(platform_fee) as revenue')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $revenueDates = $dailyRevenue->pluck('date')->map(fn($d) => Carbon::parse($d)->format('M d'))->toArray();
        $revenueValues = $dailyRevenue->pluck('revenue')->toArray();

        // 3. User Distribution
        $usersByType = User::selectRaw('user_type, COUNT(*) as count')
            ->groupBy('user_type')
            ->get();
        $userTypes = $usersByType->pluck('user_type')->toArray();
        $userCounts = $usersByType->pluck('count')->toArray();

        // 4. Order Status Distribution (New Graph)
        // Check if status column exists, otherwise mock or skip
        try {
            $ordersByStatus = CustomerOrder::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get();
            $orderStatuses = $ordersByStatus->pluck('status')->toArray();
            $orderStatusCounts = $ordersByStatus->pluck('count')->toArray();
        } catch (\Exception $e) {
            $orderStatuses = [];
            $orderStatusCounts = [];
        }

        // 5. Recent Activity
        $recentOrders = CustomerOrder::with('vendor')->latest()->take(5)->get();
        $recentThreads = ForumThread::with('user')->latest()->take(5)->get();

        // 6. Fetch Simple Analytics Data
        $analyticsData = Cache::remember('simple_analytics_stats', 300, function () {
            $website = config('services.simple_analytics.website');
            $apiKey = config('services.simple_analytics.api_key');

            if (!$website) {
                return null;
            }

            try {
                $url = "https://simpleanalytics.com/{$website}.json";
                
                $response = Http::withHeaders([
                    'Api-Key' => $apiKey,
                ])->get($url, [
                    'version' => 5,
                    'fields' => 'visitors,pageviews,seconds_on_page,histogram',
                    'start' => now()->subDays(30)->format('Y-m-d'),
                    'end' => now()->format('Y-m-d'),
                ]);

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                return null;
            }

            return null;
        });

        return view('dashboard', compact(
            'totalUsers', 'newUsersThisMonth', 'userGrowth',
            'totalRevenue', 'revenueThisMonth', 'revenueGrowth',
            'activeListings', 'totalListings',
            'totalPredictions', 'predictionsThisMonth', 'predictionGrowth',
            'revenueDates', 'revenueValues',
            'userTypes', 'userCounts',
            'orderStatuses', 'orderStatusCounts',
            'recentOrders', 'recentThreads',
            'analyticsData'
        ));
    }
}
