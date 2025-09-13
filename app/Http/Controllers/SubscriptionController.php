<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:subscriptions.view')->only(['index','success']);
        $this->middleware('permission:subscriptions.subscribe')->only('checkout');
        $this->middleware('permission:subscriptions.swap')->only('swap');
        $this->middleware('permission:subscriptions.cancel')->only('cancel');
        $this->middleware('permission:subscriptions.resume')->only('resume');
    }

    public function index(Request $r)
    {
        $user = $r->user();
        $sub  = $user->subscription('default');
        $status = $sub?->stripe_status;

        $plans = Plan::active()->orderBy('price')->get();

        return view('subscriptions.index', compact('user','sub','status','plans'));
    }

    public function checkout(Request $r, Plan $plan)
    {
        $user = $r->user();

        abort_unless($plan->stripe_price_id, 404);

        return $user->newSubscription('default', $plan->stripe_price_id)
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('subscriptions.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => route('subscriptions.index'),
            ]);
    }

    public function success(Request $r)
    {
        return redirect()->route('subscriptions.index')
            ->with('ok', 'Thanks! Your subscription is being processed.');
    }

    public function cancel(Request $r)
    {
        $sub = $r->user()->subscription('default');
        abort_unless($sub, 404);

        $sub->cancel(); // cancels at period end
        return back()->with('ok','Subscription canceled. Access remains until period end.');
    }

    public function resume(Request $r)
    {
        $sub = $r->user()->subscription('default');
        abort_unless($sub && $sub->onGracePeriod(), 404);

        $sub->resume();
        return back()->with('ok','Subscription resumed.');
    }

    public function swap(Request $r, Plan $plan)
    {
        $sub = $r->user()->subscription('default');
        abort_unless($sub && $plan->stripe_price_id, 404);

        $sub->swapAndInvoice($plan->stripe_price_id);
        return back()->with('ok','Switched to '.$plan->name.' plan.');
    }
}
