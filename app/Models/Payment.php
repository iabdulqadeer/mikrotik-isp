<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Models\Payment
 *
 * Columns (suggested):
 * - id
 * - user_id        (tenant/owner)
 * - subscription_id (nullable)
 * - plan_id         (nullable)
 * - amount          (decimal 12,2)
 * - currency        (string)
 * - status          (enum: pending|paid|failed|refunded)
 * - method          (string|null)
 * - reference       (string|null)
 * - paid_at         (timestamp|null)
 * - created_at
 * - updated_at
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'plan_id',
        'amount',
        'currency',
        'status',
        'method',
        'reference',
        'paid_at',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /*----------------------------------------
    | Relationships
    |----------------------------------------*/
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /*----------------------------------------
    | Scopes (chainable filters)
    |----------------------------------------*/

    /** Tenant scope by user_id */
    public function scopeForUser(Builder $q, int $userId): Builder
    {
        return $q->where('user_id', $userId);
    }

    /** Only successful/settled payments */
    public function scopeSuccessful(Builder $q): Builder
    {
        return $q->where('status', 'paid');
    }

    /** Payments between two datetimes (inclusive) on paid_at */
    public function scopeBetweenPaidAt(Builder $q, Carbon|string $from, Carbon|string $to): Builder
    {
        return $q->whereBetween('paid_at', [$from, $to]);
    }

    /** Limit to a specific plan */
    public function scopeForPlan(Builder $q, int $planId): Builder
    {
        return $q->where('plan_id', $planId);
    }

    /** Current month (based on paid_at) */
    public function scopeThisMonth(Builder $q, ?string $tz = null): Builder
    {
        $now = $tz ? now($tz) : now();
        return $q->whereBetween('paid_at', [
            $now->copy()->startOfMonth()->timezone('UTC'),
            $now->copy()->endOfMonth()->timezone('UTC'),
        ]);
    }

    /*----------------------------------------
    | Helpers (aggregations used by dashboard)
    |----------------------------------------*/

    public static function totalThisMonth(int $userId, ?string $tz = null): string
    {
        return static::query()
            ->forUser($userId)
            ->successful()
            ->thisMonth($tz)
            ->sum('amount');
    }

    /**
     * Monthly totals for the last N months (default: 12).
     * Returns: ['labels' => ['Jan 2025', ...], 'values' => [1234.00, ...]]
     */
    public static function monthlySeries(int $userId, int $months = 12, ?string $tz = null): array
    {
        $tz   = $tz ?: config('app.timezone', 'UTC');
        $now  = now($tz);
        $rows = [];
        $labels = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i);
            $labels[] = $m->format('M Y');
            $from = $m->copy()->startOfMonth()->timezone('UTC');
            $to   = $m->copy()->endOfMonth()->timezone('UTC');
            $rows[] = (float) static::query()
                ->forUser($userId)
                ->successful()
                ->betweenPaidAt($from, $to)
                ->sum('amount');
        }
        return ['labels' => $labels, 'values' => array_map(fn($v)=>round($v,2), $rows)];
    }
}
