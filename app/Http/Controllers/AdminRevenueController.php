<?php

namespace App\Http\Controllers;

use App\Models\OrganizationRevenue;
use Illuminate\Http\Request;

class AdminRevenueController extends Controller
{
    protected function authorizeAdmin(): void
    {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $total = OrganizationRevenue::sum('amount');

        // Use DB facade for aggregate query to avoid model casting issues and ensure SQL compatibility
        $daily = \Illuminate\Support\Facades\DB::table('organization_revenues')
            ->selectRaw('DATE(collected_at) as day, SUM(amount) as total')
            ->groupBy(\Illuminate\Support\Facades\DB::raw('DATE(collected_at)'))
            ->orderBy('day', 'desc')
            ->limit(30)
            ->get();

        $vendors = OrganizationRevenue::selectRaw('vendor_id, SUM(amount) as total')
            ->groupBy('vendor_id')
            ->orderByDesc('total')
            ->with('vendor')
            ->limit(25)
            ->get();

        return view('admin.revenue.index', compact('total', 'daily', 'vendors'));
    }

    public function exportCsv(Request $request)
    {
        $this->authorizeAdmin();

        $filename = 'platform_revenue_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function() {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['order_id','listing_id','vendor_id','buyer_id','amount','type','collected_at']);
            OrganizationRevenue::orderBy('collected_at','desc')->chunk(500, function($rows) use ($out) {
                foreach ($rows as $r) {
                    fputcsv($out, [
                        $r->order_id,
                        $r->listing_id,
                        $r->vendor_id,
                        $r->buyer_id,
                        $r->amount,
                        $r->type,
                        optional($r->collected_at)->toDateTimeString(),
                    ]);
                }
            });
            fclose($out);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }
}
