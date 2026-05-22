<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class StripeWebhookController extends CashierController
{
    /**
     * Handle invoice payment succeeded.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleInvoicePaymentSucceeded($payload)
    {
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);

        if ($user) {
            $amountPaid = $payload['data']['object']['amount_paid'] / 100;
            // Increment LTV
            $user->ltv += $amountPaid;
            $user->save();
            
            Log::info("Payment succeeded for user {$user->id}. Incremented LTV by {$amountPaid}");
        }

        return $this->successMethod();
    }

    /**
     * Handle customer subscription deleted.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleCustomerSubscriptionDeleted($payload)
    {
        parent::handleCustomerSubscriptionDeleted($payload);

        $user = $this->getUserByStripeId($payload['data']['object']['customer']);

        if ($user) {
            Log::info("Subscription deleted for user {$user->id}");
            // Additional logic (e.g., notify admin, revoke access)
        }

        return $this->successMethod();
    }
}
