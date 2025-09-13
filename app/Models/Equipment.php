<?php

namespace App\Models;

use App\Enums\EquipmentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Equipment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id','type','name','serial_number','price','paid_amount','currency','notes',
    ];

    protected $casts = [
        // keep type as string for easy DB querying; you can cast to enum if you prefer:
        // 'type' => EquipmentType::class,
        'price' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* ---------- Scopes ---------- */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!$term) return $q;
        $term = "%{$term}%";
        return $q->where(function($w) use ($term){
            $w->where('name','like',$term)
              ->orWhere('serial_number','like',$term)
              ->orWhere('type','like',$term)
              ->orWhereHas('user', fn($u)=>$u->where('name','like',$term)->orWhere('email','like',$term));
        });
    }

    public function scopeFilterType(Builder $q, ?string $type): Builder
    {
        if ($type && in_array($type, EquipmentType::options())) {
            $q->where('type', $type);
        }
        return $q;
    }

    public function getOutstandingAttribute(): float
    {
        $paid = (float) ($this->paid_amount ?? 0);
        return max((float)$this->price - $paid, 0);
    }
}
