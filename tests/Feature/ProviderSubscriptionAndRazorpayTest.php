<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\PaymentTransaction;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderSubscriptionAndRazorpayTest extends TestCase
{
    use RefreshDatabase;

    protected User $provider;
    protected User $student;
    protected SubscriptionPlan $freePlan;
    protected SubscriptionPlan $proPlan;
    protected SubscriptionPlan $elitePlan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->provider = User::where('role', UserRole::PROVIDER)->first();
        $this->student = User::where('role', UserRole::STUDENT)->first();

        $this->freePlan = SubscriptionPlan::where('slug', 'free')->first();
        $this->proPlan = SubscriptionPlan::where('slug', 'pro')->first();
        $this->elitePlan = SubscriptionPlan::where('slug', 'elite')->first();
    }

    public function test_guest_cannot_access_subscriptions(): void
    {
        $response = $this->get(route('provider.subscriptions.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_student_cannot_access_provider_subscriptions(): void
    {
        $response = $this->actingAs($this->student)->get(route('provider.subscriptions.index'));
        $response->assertRedirect(route('student.dashboard'));
    }

    public function test_provider_can_view_subscription_plans_and_active_status(): void
    {
        $response = $this->actingAs($this->provider)->get(route('provider.subscriptions.index'));
        $response->assertStatus(200);

        $response->assertSeeText('Provider Subscription');
        $response->assertSeeText('Visibility Plans');
        $response->assertSeeText('Zero Brokerage Guarantee');
        $response->assertSeeText('Pro Campus');
        $response->assertSeeText('Campus Elite');
        $response->assertSeeText('Free Starter');
    }

    public function test_provider_activating_free_plan_succeeds(): void
    {
        $response = $this->actingAs($this->provider)->postJson(route('provider.subscriptions.checkout', $this->freePlan->id));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_free' => true,
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->provider->id,
            'plan_id' => $this->freePlan->id,
            'status' => 'active',
        ]);
    }

    public function test_provider_initiating_paid_plan_creates_razorpay_order(): void
    {
        $response = $this->actingAs($this->provider)->postJson(route('provider.subscriptions.checkout', $this->proPlan->id));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_free' => false,
            'amount' => 49900,
            'currency' => 'INR',
        ]);

        $this->assertDatabaseHas('payment_transactions', [
            'user_id' => $this->provider->id,
            'amount' => 499.00,
            'status' => 'created',
        ]);
    }

    public function test_verifying_valid_payment_signature_activates_subscription(): void
    {
        $orderId = 'order_test_' . uniqid();
        PaymentTransaction::create([
            'user_id' => $this->provider->id,
            'order_id' => $orderId,
            'amount' => 499.00,
            'currency' => 'INR',
            'status' => 'created',
        ]);

        $response = $this->actingAs($this->provider)->postJson(route('provider.subscriptions.verify'), [
            'order_id' => $orderId,
            'payment_id' => 'pay_test_' . uniqid(),
            'signature' => 'mock_sig_' . $orderId,
            'plan_id' => $this->proPlan->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->provider->id,
            'plan_id' => $this->proPlan->id,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('payment_transactions', [
            'order_id' => $orderId,
            'status' => 'captured',
        ]);

        $this->assertEquals('pro', $this->provider->fresh()->providerProfile->subscription_tier);
    }

    public function test_verifying_invalid_signature_fails(): void
    {
        $orderId = 'order_test_' . uniqid();

        $response = $this->actingAs($this->provider)->postJson(route('provider.subscriptions.verify'), [
            'order_id' => $orderId,
            'payment_id' => 'pay_test_123',
            'signature' => 'invalid_tampered_signature',
            'plan_id' => $this->proPlan->id,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_webhook_handles_payment_captured(): void
    {
        $orderId = 'order_webhook_' . uniqid();
        PaymentTransaction::create([
            'user_id' => $this->provider->id,
            'order_id' => $orderId,
            'amount' => 1499.00,
            'currency' => 'INR',
            'status' => 'created',
        ]);

        $payload = json_encode([
            'event' => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_webhook_captured_999',
                        'order_id' => $orderId,
                        'amount' => 149900,
                    ],
                ],
            ],
        ]);

        $response = $this->call(
            'POST',
            route('webhooks.razorpay'),
            [],
            [],
            [],
            [
                'HTTP_X-Razorpay-Signature' => 'mock_webhook_sig',
                'CONTENT_TYPE' => 'application/json',
            ],
            $payload
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('payment_transactions', [
            'order_id' => $orderId,
            'payment_id' => 'pay_webhook_captured_999',
            'status' => 'captured',
        ]);
    }
}
