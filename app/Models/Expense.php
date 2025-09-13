<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','type','amount','spent_at','payment_method','receipt_path','description',
    ];

    protected $casts = [
        'spent_at' => 'datetime',
        'amount'   => 'decimal:2',
    ];

    // relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // scopes
    public function scopeOwned(Builder $q, $userId): Builder
    {
        return $q->where('user_id', $userId);
    }

    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!$term) return $q;
        return $q->where(function ($qq) use ($term) {
            $qq->where('type','like',"%$term%")
               ->orWhere('payment_method','like',"%$term%")
               ->orWhere('description','like',"%$term%");
        });
    }

    public function scopeSort(Builder $q, string $by='spent_at', string $dir='desc'): Builder
    {
        $by  = in_array($by, ['spent_at','type','amount','payment_method','created_at']) ? $by : 'spent_at';
        $dir = $dir === 'asc' ? 'asc' : 'desc';
        return $q->orderBy($by, $dir);
    }

    // helpers
    public function getReceiptUrlAttribute(): ?string
    {
        return $this->receipt_path ? asset('storage/'.$this->receipt_path) : null;
    }
}
