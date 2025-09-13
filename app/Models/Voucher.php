<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id','code','plan','profile','duration_minutes','price',
        'status','valid_from','valid_until','notes','created_by','used_by','used_at',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'used_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function device(){ return $this->belongsTo(Device::class); }
    public function creator(){ return $this->belongsTo(User::class, 'created_by'); }
    public function user(){ return $this->belongsTo(User::class, 'used_by'); }

    public function scopeSearch($q, $term){
        if(!$term) return $q;
        $term = "%{$term}%";
        return $q->where(function($qq) use ($term){
            $qq->where('code','like',$term)
               ->orWhere('plan','like',$term)
               ->orWhere('profile','like',$term)
               ->orWhere('notes','like',$term);
        });
    }
}