<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\Payment\RazorpayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    protected RazorpayService $razorpay;

    public function __construct(RazorpayService $razorpay)
    {
        $this->razorpay = $razorpay;
    }

    /**
     * Show available subscription plans, active status, and billing history.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $plans = SubscriptionPlan::active()->get();
        $currentSubscription = $user->currentSubscription();
        $activePlan = $user->activePlan();

        $transactions = PaymentTransaction::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view('provider.subscriptions.index', compact(
            'user',
            'plans',
            'currentSubscription',
            'activePlan',
            'transactions'
        ));
    }

    /**
     * Initiate checkout order for a plan.
     */
    public function checkout(Request $request, SubscriptionPlan $plan): JsonResponse
    {
        $user = $request->user();

        // Free plan instant activation
        if ($plan->price <= 0) {
            DB::transaction(function () use ($user, $plan) {
                // Cancel previous active subscriptions
                $user->subscriptions()->where('status', 'active')->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                ]);

                Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => null,
                ]);

                $user->providerProfile?->update([
                    'subscription_tier' => 'free',
                ]);
            });

            return response()->json([
                'success' => true,
                'is_free' => true,
                'message' => 'Free plan activated.',
                'redirect' => route('provider.subscriptions.index'),
            ]);
        }

        // Paid Plan: Create Razorpay Order
        $receipt = 'rcpt_' . $user->id . '_' . time();
        $order = $this->razorpay->createOrder($plan->price, $receipt, [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
        ]);

        PaymentTransaction::create([
            'user_id' => $user->id,
            'order_id' => $order['id'],
            'amount' => $plan->price,
            'currency' => 'INR',
            'status' => 'created',
            'raw_payload' => $order,
        ]);

        return response()->json([
            'success' => true,
            'is_free' => false,
            'order_id' => $order['id'],
            'amount' => $order['amount'],
            'currency' => $order['currency'],
            'key' => $this->razorpay->getKeyId(),
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'price' => $plan->price,
            ],
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '+91 98765 43210',
            ],
        ]);
    }

    /**
     * Verify cryptographic signature and activate subscription.
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => ['required', 'string'],
            'payment_id' => ['required', 'string'],
            'signature' => ['required', 'string'],
            'plan_id' => ['required', 'integer', 'exists:subscription_plans,id'],
        ]);

        $user = $request->user();
        $orderId = $request->input('order_id');
        $paymentId = $request->input('payment_id');
        $signature = $request->input('signature');
        $plan = SubscriptionPlan::findOrFail($request->input('plan_id'));

        $isValid = $this->razorpay->verifyPaymentSignature($orderId, $paymentId, $signature);

        if (!$isValid) {
            return response()->json([
                'success' => false,
                'message' => 'Payment signature verification failed. Please contact support.',
            ], 422);
        }

        DB::transaction(function () use ($user, $plan, $orderId, $paymentId, $signature) {
            // Cancel older active subscriptions
            $user->subscriptions()->where('status', 'active')->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Create 30-day active subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addDays(30),
            ]);

            // Update or create payment transaction record
            PaymentTransaction::updateOrCreate(
                ['order_id' => $orderId],
                [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'payment_id' => $paymentId,
                    'signature' => $signature,
                    'amount' => $plan->price,
                    'currency' => 'INR',
                    'status' => 'captured',
                    'payment_method' => 'upi',
                ]
            );

            // Update Provider Profile tier
            $user->providerProfile?->update([
                'subscription_tier' => $plan->slug,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => "Congratulations! {$plan->name} is now active on your account.",
            'redirect' => route('provider.subscriptions.index'),
        ]);
    }

    /**
     * Webhook endpoint for asynchronous Razorpay events.
     */
    public function webhook(Request $request): JsonResponse
    {
        $signature = $request->header('X-Razorpay-Signature', '');
        $rawPayload = $request->getContent();

        if (!$this->razorpay->verifyWebhookSignature($rawPayload, $signature)) {
            return response()->json(['status' => 'invalid_signature'], 400);
        }

        $payload = json_decode($rawPayload, true);
        $event = $payload['event'] ?? '';

        if ($event === 'payment.captured') {
            $payment = $payload['payload']['payment']['entity'] ?? [];
            $orderId = $payment['order_id'] ?? null;
            if ($orderId) {
                PaymentTransaction::where('order_id', $orderId)->update([
                    'payment_id' => $payment['id'] ?? null,
                    'status' => 'captured',
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
