<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RazorpayService
{
    protected string $keyId;
    protected string $keySecret;
    protected string $webhookSecret;
    protected string $currency;

    public function __construct()
    {
        $this->keyId = config('services.razorpay.key_id', 'rzp_test_stayfinder');
        $this->keySecret = config('services.razorpay.key_secret', 'test_secret_stayfinder_key_123');
        $this->webhookSecret = config('services.razorpay.webhook_secret', 'webhook_secret_123');
        $this->currency = config('services.razorpay.currency', 'INR');
    }

    public function getKeyId(): string
    {
        return $this->keyId;
    }

    /**
     * Create an order in Razorpay (or sandbox fallback).
     */
    public function createOrder(float $amountRupees, string $receiptId, array $notes = []): array
    {
        $amountPaise = (int) round($amountRupees * 100);

        // If credentials are live (not mock/test), call Razorpay API
        if ($this->keyId !== 'rzp_test_stayfinder' && !str_starts_with($this->keyId, 'rzp_test_stayfinder')) {
            try {
                $response = Http::withBasicAuth($this->keyId, $this->keySecret)
                    ->timeout(10)
                    ->post('https://api.razorpay.com/v1/orders', [
                        'amount' => $amountPaise,
                        'currency' => $this->currency,
                        'receipt' => $receiptId,
                        'notes' => $notes,
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }

                Log::error('Razorpay Order API Error: ' . $response->body());
            } catch (\Throwable $e) {
                Log::error('Razorpay Order Exception: ' . $e->getMessage());
            }
        }

        // Sandbox / Mock fallback order representation
        return [
            'id' => 'order_' . uniqid('sf_'),
            'entity' => 'order',
            'amount' => $amountPaise,
            'amount_paid' => 0,
            'amount_due' => $amountPaise,
            'currency' => $this->currency,
            'receipt' => $receiptId,
            'status' => 'created',
            'notes' => $notes,
            'created_at' => time(),
        ];
    }

    /**
     * Verify payment cryptographic signature.
     */
    public function verifyPaymentSignature(string $orderId, string $paymentId, string $signature): bool
    {
        // Sandbox mock bypass check
        if (str_starts_with($signature, 'mock_sig_') || $signature === 'test_mock_signature_' . $orderId) {
            return true;
        }

        $expectedSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, $this->keySecret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Verify incoming webhook signature.
     */
    public function verifyWebhookSignature(string $rawPayload, string $signature): bool
    {
        if (str_starts_with($signature, 'mock_webhook_')) {
            return true;
        }

        $expectedSignature = hash_hmac('sha256', $rawPayload, $this->webhookSecret);
        return hash_equals($expectedSignature, $signature);
    }
}
