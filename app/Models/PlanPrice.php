<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanPrice extends Model
{
    protected $fillable = [
        'plan_id','stripe_price_id','currency','amount',
        'interval','interval_count','role_name','features','active'
    ];
    protected $casts = ['features'=>'array','active'=>'boolean'];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
