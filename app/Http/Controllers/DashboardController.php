<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $u   = $request->user();
        $tz  = $u->timezone ?? config('app.timezone', 'UTC');
        $now = Carbon::now($tz);

        $commissionRate = (float) config('billing.commission_rate', 0.05);

        // Table names (adjust if yours differ)
        $paymentsTable   = 'payments';       // columns: id, user_id, amount, paid_at, plan_id
        $clientsTable    = 'users';          // clients list
        $subsTable       = 'subscriptions';  // columns: id, user_id, status, plan_id
        $smsLogsTable    = 'sms_messages';       // columns: id, user_id, sent_at
        $loginsTable     = 'user_logins';    // columns: id, user_id, ip, created_at
        $usageTable      = 'data_usages';    // columns: id, user_id, date, downloaded_mb, uploaded_mb, plan_id
        $packagesTable   = 'plans';          // columns: id, user_id, name, price
        $packageUseTable = 'subscriptions';  // columns: id, user_id, plan_id

        // Helper: tenant scope by user_id only
        $scopeUser = fn($q) => $q->where('user_id', $u->id);

        // ================== STAT CARDS ==================
        $monthStart = $now->copy()->startOfMonth()->timezone('UTC');
        $monthEnd   = $now->copy()->endOfMonth()->timezone('UTC');

        $amountThisMonth = (float) DB::table($paymentsTable)
            ->when(Schema::hasColumn($paymentsTable, 'user_id'), $scopeUser)
            ->whereBetween('paid_at', [$monthStart, $monthEnd])
            ->sum('amount');

        $smsBalance = (float) (optional($u->profile)->sms_balance ?? 0);

        $totalClients = DB::table($clientsTable)
            ->when(Schema::hasColumn($clientsTable, 'role'), fn($q) => $q->where('role', 'user'))
            ->when(Schema::hasColumn($clientsTable, 'user_id'), fn($q) => $q->where('user_id', $u->id))
            ->count();

        $subscribedClients = DB::table($subsTable)
            ->when(Schema::hasColumn($subsTable, 'user_id'), $scopeUser)
            ->whereIn('status', ['active'])
            ->count(); // counting active subs belonging to this user_id

        $commissionThisMonth = $amountThisMonth * $commissionRate;

        // ================== CHARTS ==================
        // Revenue & Expenses (12 months)
        $labels12   = [];
        $revenue12  = [];
        $expenses12 = [];

        for ($i = 11; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i);
            $labels12[] = $m->format('M Y');

            $start = $m->copy()->startOfMonth()->timezone('UTC');
            $end   = $m->copy()->endOfMonth()->timezone('UTC');

            $rev = (float) DB::table($paymentsTable)
                ->when(Schema::hasColumn($paymentsTable, 'user_id'), $scopeUser)
                ->whereBetween('paid_at', [$start, $end])
                ->sum('amount');
            $revenue12[] = round($rev, 2);

            $exp = (float) DB::table('expenses')
                ->when(Schema::hasTable('expenses') && Schema::hasColumn('expenses','user_id'), $scopeUser)
                ->whereBetween('spent_at', [$start, $end])
                ->sum('amount');
            $expenses12[] = round($exp, 2);
        }

        // Active users (this week)
        $weekLabels = [];
        $activeUsersWeek = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i);
            $dayStart = $d->copy()->startOfDay()->timezone('UTC');
            $dayEnd   = $d->copy()->endOfDay()->timezone('UTC');
            $weekLabels[] = $d->format('D');

            $count = DB::table($loginsTable)
                ->when(Schema::hasColumn($loginsTable, 'user_id'), $scopeUser)
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->distinct()
                ->count('user_id');
            $activeUsersWeek[] = $count;
        }

        // Retention (simple heuristic)
        $retention = [
            'labels'     => $labels12,
            'new'        => array_pad([], 12, 0), // placeholder
            'returning'  => array_map(fn($v) => max(0, (int) floor($v * 0.6)), $revenue12),
            'churn'      => array_map(fn($v) => max(0, (int) floor($v * 0.1)), $revenue12),
            'rate'       => array_map(fn($r,$e) => $r>0? round(($r-($e*0.1))/max($r,1)*100,1):0, $revenue12, $expenses12),
        ];

        // Data usage (this week)
        $dataUsage = ['labels' => $weekLabels, 'download' => [], 'upload' => []];
        foreach ($weekLabels as $idx => $label) {
            $d  = $now->copy()->subDays(6 - $idx);
            $ds = $d->copy()->startOfDay()->timezone('UTC');
            $de = $d->copy()->endOfDay()->timezone('UTC');

            $row = DB::table($usageTable)
                ->when(Schema::hasColumn($usageTable, 'user_id'), $scopeUser)
                ->selectRaw('COALESCE(SUM(downloaded_mb),0) as down, COALESCE(SUM(uploaded_mb),0) as up')
                ->whereBetween('date', [$ds, $de])
                ->first();

            $dataUsage['download'][] = (float) ($row->down ?? 0);
            $dataUsage['upload'][]   = (float) ($row->up ?? 0);
        }

        // Packages
        $pkgRows = DB::table($packagesTable)
            ->when(Schema::hasColumn($packagesTable, 'user_id'), $scopeUser)
            ->select('id','name','price')
            ->orderBy('name')->get();

        $pkgCounts = DB::table($packageUseTable)
            ->when(Schema::hasColumn($packageUseTable, 'user_id'), $scopeUser)
            ->select('plan_id', DB::raw('COUNT(*) as c'))
            ->groupBy('plan_id')->pluck('c','plan_id');

        $pkgDonutLabels = [];
        $pkgDonutValues = [];
        $pkgPerf = [];
        foreach ($pkgRows as $pkg) {
            $count = (int) ($pkgCounts[$pkg->id] ?? 0);
            $pkgDonutLabels[] = $pkg->name;
            $pkgDonutValues[] = $count;

            $pkgRevenue = (float) DB::table($paymentsTable)
                ->when(Schema::hasColumn($paymentsTable, 'user_id'), $scopeUser)
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->where('plan_id', $pkg->id)
                ->sum('amount');

            $avgUsage = (float) DB::table($usageTable)
                ->when(Schema::hasColumn($usageTable, 'user_id'), $scopeUser)
                ->whereBetween('date', [$now->copy()->startOfWeek()->timezone('UTC'), $now->copy()->endOfWeek()->timezone('UTC')])
                ->where('plan_id', $pkg->id)
                ->avg('downloaded_mb');

            $arpu = $count ? round($pkgRevenue / max($count,1), 2) : 0.0;

            $pkgPerf[] = [
                'name'            => $pkg->name,
                'price'           => (float) $pkg->price,
                'active_users'    => $count,
                'monthly_revenue' => $pkgRevenue,
                'avg_data'        => $avgUsage,
                'arpu'            => $arpu,
            ];
        }

        // Revenue forecast (simple linear)
        $histX=[]; $histY=[];
        for ($i = 5; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i);
            $start = $m->copy()->startOfMonth()->timezone('UTC');
            $end   = $m->copy()->endOfMonth()->timezone('UTC');
            $rev = (float) DB::table($paymentsTable)
                ->when(Schema::hasColumn($paymentsTable, 'user_id'), $scopeUser)
                ->whereBetween('paid_at', [$start, $end])
                ->sum('amount');
            $histX[] = 6 - $i;
            $histY[] = $rev;
        }
        $n = max(count($histX),1);
        $sumX = array_sum($histX) ?: 1;
        $sumY = array_sum($histY) ?: 0;
        $sumXY = 0; $sumX2 = 0;
        for ($i=0;$i<$n;$i++){ $sumXY += $histX[$i]*$histY[$i]; $sumX2 += $histX[$i]*$histX[$i]; }
        $slope = ($n*$sumXY - $sumX*$sumY) / max(($n*$sumX2 - $sumX*$sumX), 1);
        $intercept = ($sumY - $slope*$sumX)/$n;

        $forecastLabels=[]; $histSeries=[]; $forecastSeries=[];
        for ($i = 2; $i >= -3; $i--) {
            $m = $now->copy()->subMonths($i);
            $forecastLabels[] = $m->format('M');
            $x = count($histX) + (3-$i);
            $forecastSeries[] = max(0, round($intercept + $slope * $x, 2));
        }
        for ($i = 2; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i);
            $start = $m->copy()->startOfMonth()->timezone('UTC');
            $end   = $m->copy()->endOfMonth()->timezone('UTC');
            $histSeries[] = (float) DB::table($paymentsTable)
                ->when(Schema::hasColumn($paymentsTable, 'user_id'), $scopeUser)
                ->whereBetween('paid_at', [$start, $end])
                ->sum('amount');
        }
        $forecastLow  = array_map(fn($v)=>round($v*0.85,2), $forecastSeries);
        $forecastHigh = array_map(fn($v)=>round($v*1.15,2), $forecastSeries);

        // Sent SMS (this week)
        $smsWeek = [];
        foreach ($weekLabels as $idx => $label) {
            $d  = $now->copy()->subDays(6 - $idx);
            $ds = $d->copy()->startOfDay()->timezone('UTC');
            $de = $d->copy()->endOfDay()->timezone('UTC');
            $smsWeek[] = DB::table($smsLogsTable)
                ->when(Schema::hasColumn($smsLogsTable, 'user_id'), $scopeUser)
                ->whereBetween('sent_at', [$ds, $de])
                ->count();
        }

        // User registrations (this week)
        $regsWeek = [];
        foreach ($weekLabels as $idx => $label) {
            $d  = $now->copy()->subDays(6 - $idx);
            $ds = $d->copy()->startOfDay()->timezone('UTC');
            $de = $d->copy()->endOfDay()->timezone('UTC');
            $regsWeek[] = DB::table($clientsTable)
                ->when(Schema::hasColumn($clientsTable, 'role'), fn($q) => $q->where('role','user'))
                ->when(Schema::hasColumn($clientsTable, 'user_id'), fn($q) => $q->where('user_id',$u->id))
                ->whereBetween('created_at', [$ds, $de])
                ->count();
        }

        // Most active users (last 30 days) — by user_id
        $topUsers = DB::table($loginsTable)
            ->when(Schema::hasColumn($loginsTable, 'user_id'), $scopeUser)
            ->where('created_at','>=',$now->copy()->subDays(30)->timezone('UTC'))
            ->select('user_id', DB::raw('COUNT(*) as sessions'))
            ->groupBy('user_id')
            ->orderByDesc('sessions')
            ->limit(5)
            ->get();

        $accountExpiresAt = optional($u->subscription_expires_at)->timezone($tz);

        $hour = (int) $now->format('H');
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

        return view('dashboard', [
            'tz' => $tz,
            'now' => $now,
            'greeting' => $greeting,
            'stats' => [
                'amount_this_month'  => $amountThisMonth,
                'sms_balance'        => $smsBalance,
                'total_clients'      => $totalClients,
                'subscribed_clients' => $subscribedClients,
                'commission'         => $commissionThisMonth,
                'expires_at'         => $accountExpiresAt,
            ],
            'charts' => [
                'revenue_expenses' => ['labels'=>$labels12,'revenue'=>$revenue12,'expenses'=>$expenses12],
                'active_users_week'=> ['labels'=>$weekLabels,'active'=>$activeUsersWeek],
                'retention'        => $retention,
                'data_usage'       => $dataUsage,
                'packages'         => ['labels'=>$pkgDonutLabels,'values'=>$pkgDonutValues],
                'forecast'         => ['labels'=>$forecastLabels,'hist'=>$histSeries,'pred'=>$forecastSeries,'low'=>$forecastLow,'high'=>$forecastHigh],
                'sms_week'         => ['labels'=>$weekLabels,'values'=>$smsWeek],
                'registrations'    => ['labels'=>$weekLabels,'values'=>$regsWeek],
            ],
            'pkgPerf'         => $pkgPerf,
            'topUsers'        => $topUsers,
            'currency'        => $u->display_currency ?? config('currency.default.code', 'USD'),
            'commissionRate'  => $commissionRate,
        ]);
    }
}
