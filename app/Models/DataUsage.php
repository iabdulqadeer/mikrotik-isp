<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Models\DataUsage
 *
 * Columns (suggested):
 * - id
 * - user_id         (tenant/owner)
 * - subscription_id (nullable)
 * - plan_id         (nullable)
 * - date            (date)
 * - downloaded_mb   (decimal 12,2)
 * - uploaded_mb     (decimal 12,2)
 * - created_at
 * - updated_at
 */
class DataUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'plan_id',
        'date',
        'downloaded_mb',
        'uploaded_mb',
    ];

    protected $casts = [
        'date'          => 'date',
        'downloaded_mb' => 'decimal:2',
        'uploaded_mb'   => 'decimal:2',
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
    | Scopes
    |----------------------------------------*/

    /** Tenant scope by user_id */
    public function scopeForUser(Builder $q, int $userId): Builder
    {
        return $q->where('user_id', $userId);
    }

    /** Filter by a week window (Mon–Sun by default) in tenant TZ, persisted as UTC dates */
    public function scopeWeek(Builder $q, ?Carbon $pivot = null, ?string $tz = null): Builder
    {
        $tz    = $tz ?: config('app.timezone', 'UTC');
        $pivot = $pivot ?: now($tz);
        $from  = $pivot->copy()->startOfWeek()->toDateString();
        $to    = $pivot->copy()->endOfWeek()->toDateString();
        return $q->whereBetween('date', [$from, $to]);
    }

    /** Filter by date range (Y-m-d strings or Carbon) */
    public function scopeBetweenDates(Builder $q, Carbon|string $from, Carbon|string $to): Builder
    {
        $from = $from instanceof Carbon ? $from->toDateString() : $from;
        $to   = $to   instanceof Carbon ? $to->toDateString()   : $to;
        return $q->whereBetween('date', [$from, $to]);
    }

    /** Optional: filter by plan */
    public function scopeForPlan(Builder $q, int $planId): Builder
    {
        return $q->where('plan_id', $planId);
    }

    /*----------------------------------------
    | Helpers (aggregations for charts)
    |----------------------------------------*/

    /**
     * Returns daily usage arrays for a 7-day window (Mon–Sun).
     * ['labels'=>['Mon','Tue',...], 'download'=>[...], 'upload'=>[...]]
     */
    public static function weeklySeries(int $userId, ?string $tz = null): array
    {
        $tz  = $tz ?: config('app.timezone','UTC');
        $now = now($tz);

        $labels   = [];
        $download = [];
        $upload   = [];

        for ($i = 6; $i >= 0; $i--) {
            $d  = $now->copy()->subDays($i);
            $labels[] = $d->format('D');
            $from = $d->copy()->startOfDay()->toDateString();
            $to   = $d->copy()->endOfDay()->toDateString();

            $row = static::query()
                ->forUser($userId)
                ->betweenDates($from, $to)
                ->selectRaw('COALESCE(SUM(downloaded_mb),0) as down, COALESCE(SUM(uploaded_mb),0) as up')
                ->first();

            $download[] = (float) ($row->down ?? 0);
            $upload[]   = (float) ($row->up ?? 0);
        }

        return ['labels'=>$labels, 'download'=>$download, 'upload'=>$upload];
    }
}
