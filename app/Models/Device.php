<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Device extends Model
{
protected $fillable = [
'uuid','name','host','port','ssl','username','password_encrypted','identity','options','provision_token','last_seen_at','created_by'
];


protected $casts = [
'ssl' => 'bool',
'options' => 'array',
'last_seen_at' => 'datetime',
'password_encrypted' => 'encrypted',
];


protected static function booted() {
static::creating(function ($m) {
$m->uuid = $m->uuid ?: (string) Str::uuid();
$m->provision_token = $m->provision_token ?: Str::random(40);
});
}
}
