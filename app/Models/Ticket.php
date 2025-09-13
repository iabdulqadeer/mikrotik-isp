<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Ticket extends Model
{
    protected $fillable = [
        'number','user_id','opened_by','subject','priority','status','closed_at','owner_id',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function opener(): BelongsTo { return $this->belongsTo(User::class, 'opened_by'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function messages(): HasMany { return $this->hasMany(TicketMessage::class)->latest(); }

    /** Local scope for keyword search */
    public function scopeSearch($q, ?string $term)
    {
        if (!$term) return $q;
        $like = '%'.$term.'%';
        return $q->where(function($qq) use ($like) {
            $qq->where('number', 'like', $like)
               ->orWhere('subject', 'like', $like)
               ->orWhere('status', 'like', $like)
               ->orWhere('priority', 'like', $like);
        });
    }
}
