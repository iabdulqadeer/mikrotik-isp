<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanPrice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlanPriceController extends Controller
{
    public function index()
    {
        $items = PlanPrice::with('plan')->latest()->paginate(20);
        return view('plan_prices.index', compact('items'));
    }

    public function create()
    {
        $plans = Plan::active()->orderBy('name')->get();
        return view('plan_prices.create', compact('plans'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'plan_id'         => ['required','exists:plans,id'],
            'stripe_price_id' => ['required','string','max:191','unique:plan_prices,stripe_price_id'],
            'currency'        => ['required','string','max:10'],
            'amount'          => ['required','integer','min:0'], // cents
            'interval'        => ['required', Rule::in(['day','week','month','year'])],
            'interval_count'  => ['required','integer','min:1','max:52'],
            'role_name'       => ['nullable','string','max:50'],
            'features'        => ['nullable','array'],
            'active'          => ['boolean'],
        ]);

        $data['active'] = $r->boolean('active');
        PlanPrice::create($data);

        return redirect()->route('plan-prices.index')->with('ok','Plan price created.');
    }

    public function edit(PlanPrice $plan_price)
    {
        $plans = Plan::orderBy('name')->get();
        return view('plan_prices.edit', ['item'=>$plan_price, 'plans'=>$plans]);
    }

    public function update(Request $r, PlanPrice $plan_price)
    {
        $data = $r->validate([
            'plan_id'         => ['required','exists:plans,id'],
            'stripe_price_id' => ['required','string','max:191', Rule::unique('plan_prices','stripe_price_id')->ignore($plan_price->id)],
            'currency'        => ['required','string','max:10'],
            'amount'          => ['required','integer','min:0'],
            'interval'        => ['required', Rule::in(['day','week','month','year'])],
            'interval_count'  => ['required','integer','min:1','max:52'],
            'role_name'       => ['nullable','string','max:50'],
            'features'        => ['nullable','array'],
            'active'          => ['boolean'],
        ]);

        $data['active'] = $r->boolean('active');
        $plan_price->update($data);

        return back()->with('ok','Plan price updated.');
    }

    public function destroy(PlanPrice $plan_price)
    {
        $plan_price->delete();
        return back()->with('ok','Plan price deleted.');
    }
}
