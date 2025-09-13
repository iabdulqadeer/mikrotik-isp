<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id','name','email','phone','company','address','city','state','postal_code','country',
        'source','status','last_contact_at','next_follow_up_at','notes'
    ];

    protected $casts = [
        'last_contact_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
    ];

    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }

    /** Quick search across common fields */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!$term) return $q;
        $like = '%'.str_replace(' ', '%', $term).'%';
        return $q->where(function($qq) use ($like) {
            $qq->where('name', 'like', $like)
               ->orWhere('email', 'like', $like)
               ->orWhere('phone', 'like', $like)
               ->orWhere('company', 'like', $like)
               ->orWhere('address', 'like', $like);
        });
    }

    /** Simple sortable helper (whitelist columns) */
    public function scopeSort(Builder $q, ?string $by, ?string $dir): Builder
    {
        $by  = in_array($by, ['name','email','phone','status','created_at']) ? $by : 'created_at';
        $dir = in_array($dir, ['asc','desc']) ? $dir : 'desc';
        return $q->orderBy($by, $dir);
    }
}
