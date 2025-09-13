<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BillingController extends Controller
{


    public function __construct()
    {
        $this->middleware('permission:subscriptions.billing_portal')->only('portal');
        $this->middleware('permission:subscriptions.view_invoices')->only(['invoices','download']);
    }


    public function portal(Request $r)
    {
        // Stripe Billing Portal for payment methods & billing details
        return $r->user()->redirectToBillingPortal(route('billing.invoices'));
    }

    public function invoices(Request $r)
    {
        $invoices = $r->user()->invoices();
        return view('billing.invoices', compact('invoices'));
    }

    public function download(Request $r, string $id)
    {
        return $r->user()->downloadInvoice($id, [
            'vendor'  => config('app.name'),
            'product' => 'Subscription',
        ]);
    }
}
