<?php

namespace App\Livewire\Public;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Services\Membership\MembershipService;
use App\Services\Payment\PaymentManager;
use App\Models\MembershipSetting;
use App\Models\PaymentTransaction;
use Exception;

class UpgradeModal extends Component
{
    public bool $show = false;
    public bool $paymentSuccess = false;
    public string $paymentReference = '';

    public ?MembershipSetting $globalSetting = null;
    public bool $isMember = false;

    protected $listeners = ['open-upgrade-modal' => 'open'];

    public function mount(): void
    {
        $this->loadMembershipData();
    }

    protected function loadMembershipData(): void
    {
        $membershipService = app(MembershipService::class);
        $this->globalSetting = $membershipService->getCurrentSettings();
        $this->isMember = Auth::user()?->isMember() ?? false;
    }

    /**
     * Open the modal.
     */
    public function open(): void
    {
        $this->loadMembershipData();
        
        if ($this->isMember) {
            return;
        }
        
        $this->reset('paymentSuccess', 'paymentReference');
        $this->show = true;
    }

    /**
     * Close the modal.
     */
    public function close(): void
    {
        $this->show = false;
    }

    /**
     * Initiate payment processing.
     */
    public function processPurchase()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            if (!$this->globalSetting) {
                throw new Exception("Membership configuration is missing.");
            }

            $gateway = config('payments.default_gateway', 'dummy');
            $paymentManager = app(PaymentManager::class);
            
            $cardData = [];
            if ($gateway === 'dummy') {
                $cardData = [
                    'number' => '4111111111111111',
                    'name' => Auth::user()->name,
                    'expiry' => '12/30',
                    'cvv' => '123'
                ];
            }

            $result = $paymentManager->purchaseMembership(
                Auth::user(),
                (float) $this->globalSetting->price_inr,
                $cardData,
                $this->globalSetting->id
            );

            if (!$result->success) {
                throw new Exception($result->message ?? "Failed to initiate payment.");
            }

            if ($gateway === 'dummy') {
                $this->paymentSuccess = true;
                $this->paymentReference = $result->reference;
                $this->isMember = true;
                $this->dispatch('membership-activated');
            } else {
                $mode = config('payments.mode', 'test');
                $key = config("payments.gateways.razorpay.{$mode}.key_id");

                $this->dispatch('start-razorpay-checkout', [
                    'key' => $key,
                    'amount' => (int) round($this->globalSetting->price_inr * 100),
                    'currency' => config('payments.currency', 'INR'),
                    'order_id' => $result->gatewayOrderId,
                    'reference' => $result->reference,
                    'user' => [
                        'name' => Auth::user()->name,
                        'email' => Auth::user()->email,
                        'phone' => Auth::user()->whatsapp_phone ?? '',
                    ],
                    'meta' => [
                        'title' => $this->globalSetting->title ?? 'AnthroConnect Membership',
                        'description' => $this->globalSetting->description ?? 'Premium access',
                    ]
                ]);
            }

        } catch (Exception $e) {
            $this->addError('payment', $e->getMessage());
        }
    }

    /**
     * Secure callback from frontend after backend verification passes.
     */
    public function handlePaymentVerified(string $reference)
    {
        $transaction = PaymentTransaction::where('reference', $reference)
            ->where('user_id', Auth::id())
            ->first();

        if ($transaction && ($transaction->isCaptured() || $transaction->isAuthorized())) {
            $this->paymentSuccess = true;
            $this->paymentReference = $reference;
            $this->isMember = true;
            $this->dispatch('membership-activated');
        } else {
            $this->addError('payment', 'Payment verification failed on the server.');
        }
    }

    public function render()
    {
        return view('livewire.public.upgrade-modal');
    }
}
