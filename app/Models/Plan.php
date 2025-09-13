<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','speed_down_kbps','speed_up_kbps','price','billing_cycle','active','stripe_price_id','owner_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    /** Quick labels */
    public function getSpeedLabelAttribute(): string
    {
        return number_format($this->speed_down_kbps).'↓ / '.number_format($this->speed_up_kbps).'↑ kbps';
    }

    /** Scopes for filtering */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!$term) return $q;
        return $q->where(function($qq) use ($term){
            $qq->where('name','like',"%{$term}%")
               ->orWhere('price','like',"%{$term}%");
        });
    }

    public function scopeCycle(Builder $q, ?string $cycle): Builder
    {
        if (!$cycle) return $q;
        return $q->where('billing_cycle',$cycle);
    }

    public function scopeActiveState(Builder $q, ?string $state): Builder
    {
        if ($state === '1') return $q->where('active', true);
        if ($state === '0') return $q->where('active', false);
        return $q;
    }


    public function prices()
    {
        return $this->hasMany(PlanPrice::class);
    }

    public function scopeActive($q){ return $q->where('active', true); }

}
