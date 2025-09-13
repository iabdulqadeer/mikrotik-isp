<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SmsMessage extends Model
{
    protected $fillable = [
        'user_id','phone','message','status','twilio_sid','sent_at','delivered_at','error_code','error_message','meta'
    ];

    protected $casts = [
        'sent_at'      => 'datetime',
        'delivered_at' => 'datetime',
        'meta'         => 'array',
    ];

    public function user() { return $this->belongsTo(User::class); }

    // short preview used in table
    protected function short(): Attribute {
        return Attribute::get(fn() => str($this->message)->limit(70));
    }
}
