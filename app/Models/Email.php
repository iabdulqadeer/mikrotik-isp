<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Email extends Model
{
    protected $fillable = [
        'user_id','subject','message','to_email','cc','bcc','status','sent_at','error_message'
    ];

    protected $casts = [
        'cc' => 'array',
        'bcc' => 'array',
        'sent_at' => 'datetime',
    ];

    /* Simple search over list UI */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!$term) return $q;
        return $q->where(function($qq) use ($term) {
            $qq->where('subject', 'like', "%{$term}%")
               ->orWhere('to_email', 'like', "%{$term}%")
               ->orWhere('message', 'like', "%{$term}%")
               ->orWhere('status', 'like', "%{$term}%");
        });
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
