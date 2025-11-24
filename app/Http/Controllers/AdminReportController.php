<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\MarketplaceListing;
use App\Models\RiskPredictionLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AdminReportExport;

class AdminReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function generate(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $type = $request->input('type', 'pdf'); // pdf or excel

        $data = $this->getReportData($startDate, $endDate);

        if ($type === 'excel' || $type === 'csv') {
            return Excel::download(new AdminReportExport($data, $startDate, $endDate), 'admin_report_' . now()->format('Y_m_d') . '.xlsx');
        }

        // PDF
        $pdf = Pdf::loadView('admin.reports.pdf', compact('data', 'startDate', 'endDate'));
        return $pdf->download('admin_report_' . now()->format('Y_m_d') . '.pdf');
    }

    private function getReportData($startDate, $endDate)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // 1. User Growth
        $totalUsers = User::count();
        $newUsers = User::whereBetween('created_at', [$start, $end])->count();
        $usersByType = User::selectRaw('user_type, COUNT(*) as count')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('user_type')
            ->pluck('count', 'user_type')
            ->toArray();

        // 2. Revenue
        $totalRevenue = Order::whereBetween('created_at', [$start, $end])->sum('total');
        $ordersCount = Order::whereBetween('created_at', [$start, $end])->count();
        $dailyRevenue = Order::selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 3. Listings
        $newListings = MarketplaceListing::whereBetween('created_at', [$start, $end])->count();
        
        // 4. Risk Predictions
        $predictionsCount = RiskPredictionLog::whereBetween('created_at', [$start, $end])->count();

        return [
            'total_users' => $totalUsers,
            'new_users' => $newUsers,
            'users_by_type' => $usersByType,
            'total_revenue' => $totalRevenue,
            'orders_count' => $ordersCount,
            'daily_revenue' => $dailyRevenue,
            'new_listings' => $newListings,
            'predictions_count' => $predictionsCount,
        ];
    }
}
