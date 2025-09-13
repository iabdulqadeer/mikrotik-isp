<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use Laravel\Cashier\Cashier;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stripe Webhook controller for Laravel Cashier.
 *
 * Handles:
 * - customer.subscription.created
 * - customer.subscription.updated
 * - customer.subscription.deleted
 * - invoice.payment_succeeded
 * - invoice.payment_failed
 * - customer.updated
 * - customer.deleted
 * - payment_method.card_automatically_updated
 *
 * Notes:
 * - Keep route CSRF-exempt and point to ->handleWebhook().
 * - Set STRIPE_WEBHOOK_SECRET in .env (from `stripe listen`).
 */
class WebhookController extends CashierWebhookController
{
    /** ---------------- Subscriptions ---------------- */

    // Stripe: customer.subscription.created
    protected function handleCustomerSubscriptionCreated(array $payload)
    {
        // First let Cashier do its syncing
        $response = parent::handleCustomerSubscriptionCreated($payload);

        $sub = $payload['data']['object'] ?? [];
        $customerId = $sub['customer'] ?? null;
        $billable = $customerId ? Cashier::findBillable($customerId) : null;

        Log::info('🧾 Subscription created', [
            'subscription' => $sub['id'] ?? null,
            'status'       => $sub['status'] ?? null,
            'customer'     => $customerId,
            'billable_id'  => $billable?->getKey(),
        ]);

        // TODO: e.g., grant features, send welcome-to-plan email, etc.

        return $response;
    }

    // Stripe: customer.subscription.updated
    protected function handleCustomerSubscriptionUpdated(array $payload)
    {
        $response = parent::handleCustomerSubscriptionUpdated($payload);

        $sub = $payload['data']['object'] ?? [];
        $customerId = $sub['customer'] ?? null;
        $billable = $customerId ? Cashier::findBillable($customerId) : null;

        Log::info('🧾 Subscription updated', [
            'subscription' => $sub['id'] ?? null,
            'status'       => $sub['status'] ?? null,
            'customer'     => $customerId,
            'billable_id'  => $billable?->getKey(),
        ]);

        // TODO: reflect plan/quantity/period changes in your app

        return $response;
    }

    // Stripe: customer.subscription.deleted
    protected function handleCustomerSubscriptionDeleted(array $payload)
    {
        $response = parent::handleCustomerSubscriptionDeleted($payload);

        $sub = $payload['data']['object'] ?? [];
        $customerId = $sub['customer'] ?? null;
        $billable = $customerId ? Cashier::findBillable($customerId) : null;

        Log::info('🧾 Subscription deleted', [
            'subscription' => $sub['id'] ?? null,
            'customer'     => $customerId,
            'billable_id'  => $billable?->getKey(),
        ]);

        // TODO: revoke features, downgrade access, notify user

        return $response;
    }

    /** ---------------- Invoices ---------------- */

    // Stripe: invoice.payment_succeeded
    protected function handleInvoicePaymentSucceeded(array $payload)
    {
        $response = parent::handleInvoicePaymentSucceeded($payload);

        $invoice = $payload['data']['object'] ?? [];
        $customerId = $invoice['customer'] ?? null;
        $billable = $customerId ? Cashier::findBillable($customerId) : null;

        Log::info('✅ Invoice payment succeeded', [
            'invoice'     => $invoice['id'] ?? null,
            'amount_paid' => $invoice['amount_paid'] ?? null,
            'customer'    => $customerId,
            'billable_id' => $billable?->getKey(),
        ]);

        // TODO: credit wallet, extend access, send receipt, etc.

        return $response;
    }

    // Stripe: invoice.payment_failed
    protected function handleInvoicePaymentFailed(array $payload)
    {
        $response = parent::handleInvoicePaymentFailed($payload);

        $invoice = $payload['data']['object'] ?? [];
        $customerId = $invoice['customer'] ?? null;
        $billable = $customerId ? Cashier::findBillable($customerId) : null;

        Log::warning('❌ Invoice payment failed', [
            'invoice'     => $invoice['id'] ?? null,
            'customer'    => $customerId,
            'billable_id' => $billable?->getKey(),
        ]);

        // TODO: notify user, start dunning, set grace period flag, etc.

        return $response;
    }

    /** ---------------- Customers ---------------- */

    // Stripe: customer.updated
    protected function handleCustomerUpdated(array $payload)
    {
        $response = parent::handleCustomerUpdated($payload);

        $customer = $payload['data']['object'] ?? [];
        $customerId = $customer['id'] ?? null;
        $billable = $customerId ? Cashier::findBillable($customerId) : null;

        Log::info('👤 Customer updated', [
            'customer'    => $customerId,
            'email'       => $customer['email'] ?? null,
            'billable_id' => $billable?->getKey(),
        ]);

        // TODO: sync email/name/default payment method into your DB

        return $response;
    }

    // Stripe: customer.deleted
    protected function handleCustomerDeleted(array $payload)
    {
        $response = parent::handleCustomerDeleted($payload);

        $customer = $payload['data']['object'] ?? [];
        $customerId = $customer['id'] ?? null;
        $billable = $customerId ? Cashier::findBillable($customerId) : null;

        Log::info('👤 Customer deleted', [
            'customer'    => $customerId,
            'billable_id' => $billable?->getKey(),
        ]);

        // TODO: mark user as inactive/cleanup local data if desired

        return $response;
    }

    /** ---------------- Payment Method auto-updates ---------------- */

    // Stripe: payment_method.card_automatically_updated
    protected function handlePaymentMethodCardAutomaticallyUpdated(array $payload)
    {
        $pm = $payload['data']['object'] ?? [];
        $customerId = $pm['customer'] ?? null;
        $billable = $customerId ? Cashier::findBillable($customerId) : null;

        Log::info('💳 Card automatically updated', [
            'payment_method' => $pm['id'] ?? null,
            'customer'       => $customerId,
            'brand'          => $pm['card']['brand'] ?? null,
            'last4'          => $pm['card']['last4'] ?? null,
            'exp_month'      => $pm['card']['exp_month'] ?? null,
            'exp_year'       => $pm['card']['exp_year'] ?? null,
            'billable_id'    => $billable?->getKey(),
        ]);

        // TODO: reflect new last4/expiry in your UI

        return new Response('Handled', 200);
    }
}
