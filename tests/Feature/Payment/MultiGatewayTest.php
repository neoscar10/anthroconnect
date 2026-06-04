<?php

namespace Tests\Feature\Payment;

use App\Models\PaymentSetting;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\Payment\GatewayRegistry;
use App\Services\Payment\PaymentManager;
use App\Services\Payment\PaymentSettingsService;
use App\Enums\Payment\PaymentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Exception;
use Tests\TestCase;

class MultiGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed Spatie roles and permissions
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Seed initial payment settings
        PaymentSetting::query()->delete();
        PaymentSetting::create([
            'gateway' => 'razorpay',
            'display_name' => 'Razorpay',
            'is_enabled' => true,
            'is_default' => true,
            'sort_order' => 1,
        ]);
        PaymentSetting::create([
            'gateway' => 'cashfree',
            'display_name' => 'Cashfree',
            'is_enabled' => false,
            'is_default' => false,
            'sort_order' => 2,
        ]);
    }

    /**
     * Test Payment Settings Service
     */
    public function test_payment_settings_service_returns_enabled_and_default(): void
    {
        $service = app(PaymentSettingsService::class);
        $enabled = $service->getEnabledGateways();
        $default = $service->getDefaultGateway();

        $this->assertCount(1, $enabled);
        $this->assertSame('razorpay', $enabled->first()->gateway);
        $this->assertSame('razorpay', $default->gateway);
    }

    public function test_gateway_enabling(): void
    {
        $service = app(PaymentSettingsService::class);
        $service->enableGateway('cashfree');

        $this->assertTrue(PaymentSetting::where('gateway', 'cashfree')->first()->is_enabled);
    }

    public function test_gateway_disabling(): void
    {
        $service = app(PaymentSettingsService::class);
        
        // First enable cashfree so we have another enabled gateway
        $service->enableGateway('cashfree');
        
        // Then disable razorpay (which is default, so we should change default first)
        $service->setDefaultGateway('cashfree');
        $service->disableGateway('razorpay');

        $this->assertFalse(PaymentSetting::where('gateway', 'razorpay')->first()->is_enabled);
    }

    public function test_default_gateway_assignment(): void
    {
        $service = app(PaymentSettingsService::class);
        
        // Must enable first before setting default
        $service->enableGateway('cashfree');
        $service->setDefaultGateway('cashfree');

        $this->assertSame('cashfree', $service->getDefaultGateway()->gateway);
    }

    public function test_disabled_gateway_cannot_be_set_as_default(): void
    {
        $service = app(PaymentSettingsService::class);
        
        $this->expectException(Exception::class);
        $service->setDefaultGateway('cashfree'); // Cashfree is disabled initially
    }

    public function test_last_gateway_protection(): void
    {
        $service = app(PaymentSettingsService::class);
        
        // Try to disable razorpay without setting another default/enabled
        $this->expectException(Exception::class);
        $service->disableGateway('razorpay');
    }

    /**
     * Test PaymentManager gateway resolution & Gateway registry resolution
     */
    public function test_gateway_registry_resolves_enabled_gateway(): void
    {
        $registry = app(GatewayRegistry::class);
        $gateway = $registry->resolve('razorpay');

        $this->assertInstanceOf(\App\Services\Payment\Gateways\RazorpayGateway::class, $gateway);
    }

    public function test_gateway_registry_throws_unsupported_exception_for_disabled_gateway(): void
    {
        $registry = app(GatewayRegistry::class);
        
        $this->expectException(\App\Services\Payment\Exceptions\UnsupportedGatewayException::class);
        $registry->resolve('cashfree');
    }

    public function test_payment_manager_resolves_default_gateway(): void
    {
        $manager = app(PaymentManager::class);
        $gateway = $manager->gateway();

        $this->assertInstanceOf(\App\Services\Payment\Gateways\RazorpayGateway::class, $gateway);
    }

    /**
     * Test frontend gateway discovery endpoint
     */
    public function test_frontend_gateway_discovery_endpoint(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->getJson(route('payments.gateways'));

        $response->assertOk();
        $response->assertJson([
            'enabled_gateways' => ['razorpay'],
            'default_gateway' => 'razorpay',
        ]);
    }

    /**
     * Test analytics calculations & Admin payment settings permissions
     */
    public function test_admin_settings_livewire_permissions(): void
    {
        $user = User::factory()->create();
        
        // Non-admin should be forbidden or redirected
        $response = $this->actingAs($user)->get(route('admin.settings.payments'));
        $response->assertStatus(403);
    }

    public function test_admin_settings_livewire_works_for_admin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');

        // Seed some mock transactions to calculate stats
        PaymentTransaction::create([
            'user_id' => $admin->id,
            'amount' => 500.00,
            'currency' => 'INR',
            'gateway' => \App\Enums\Payment\PaymentGateway::RAZORPAY,
            'purpose' => \App\Enums\Payment\PaymentPurpose::MEMBERSHIP_UPGRADE,
            'reference' => 'ACMEM-11111',
            'status' => PaymentStatus::CAPTURED,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.settings.payments'));
        $response->assertOk();
        $response->assertSee('Razorpay');
        $response->assertSee('Cashfree');
    }
}
