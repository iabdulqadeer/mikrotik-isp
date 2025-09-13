<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name','type','banner_text','image_size','image_path','start_date','end_date','views'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /* ---- Derived state ---- */
    public function getIsRunningAttribute(): bool
    {
        $today = Carbon::today();
        $starts = $this->start_date && $this->start_date->lte($today);
        $notEnded = is_null($this->end_date) || $this->end_date->gte($today);
        return $starts && $notEnded;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_running ? 'Running' : 'Expired';
    }

    /* ---- Scopes ---- */
    public function scopeRunning(Builder $q): Builder
    {
        $today = Carbon::today()->toDateString();
        return $q->whereDate('start_date','<=',$today)
                ->where(function($qq) use ($today){
                    $qq->whereNull('end_date')->orWhereDate('end_date','>=',$today);
                });
    }

    public function scopeExpired(Builder $q): Builder
    {
        $today = now()->toDateString();
        return $q->whereNot(function($qq) use ($today){
            $qq->whereDate('start_date','<=',$today)
               ->where(function($q2) use ($today){
                    $q2->whereNull('end_date')->orWhereDate('end_date','>=',$today);
               });
        });
    }

    public function scopeBanner(Builder $q): Builder { return $q->where('type','banner'); }
    public function scopeImage(Builder $q): Builder { return $q->where('type','image'); }

    /* ---- Helpers ---- */
    public function incrementViews(): void
    {
        $this->views++;
        $this->saveQuietly();
    }

    public function imageUrl(?string $disk='public'): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }
}
