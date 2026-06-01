<?php

declare(strict_types=1);

namespace Modules\Payment\Services;

use Modules\Order\Models\Order;
use Modules\Payment\DTOs\StripeCheckoutResultDto;
use Modules\Payment\Exceptions\InvalidWebhookSignatureException;
use Modules\Payment\Exceptions\PaymentFailedException;
use Stripe\Event;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

/**
 * Thin wrapper over the Stripe SDK. Holds no Eloquent state — it's just an I/O adapter.
 * Persistence of payment records lives in PaymentService → PaymentRepository.
 */
class StripeService
{
    private StripeClient $client;

    public function __construct()
    {
        $this->client = new StripeClient((string) config('services.stripe.secret'));
    }

    public function createCheckoutSession(
        Order $order,
        string $successUrl,
        string $cancelUrl,
    ): StripeCheckoutResultDto {
        try {
            $session = $this->client->checkout->sessions->create([
                'mode' => 'payment',
                'customer_email' => $order->customer?->email,
                'line_items' => [[
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => $order->currency,
                        'unit_amount' => (int) round((float) $order->total * 100),
                        'product_data' => [
                            'name' => 'Marketplace Order #'.$order->id,
                        ],
                    ],
                ]],
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'order_id' => (string) $order->id,
                ],
            ]);
        } catch (ApiErrorException $e) {
            throw new PaymentFailedException('Unable to start Stripe checkout: '.$e->getMessage(), 0, $e);
        }

        return new StripeCheckoutResultDto(
            sessionId: (string) $session->id,
            checkoutUrl: (string) $session->url,
        );
    }

    public function verifyWebhook(string $payload, string $signatureHeader): Event
    {
        try {
            return Webhook::constructEvent(
                $payload,
                $signatureHeader,
                (string) config('services.stripe.webhook_secret'),
            );
        } catch (SignatureVerificationException $e) {
            throw new InvalidWebhookSignatureException();
        }
    }
}
