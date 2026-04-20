<?php

namespace App\Livewire\Public;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Services\Membership\MembershipService;
use App\Services\Membership\MembershipPurchaseService;
use App\Models\MembershipSetting;
use App\Models\UserMembership;
use Exception;

class UpgradeModal extends Component
{
    public bool $show = false;
    public bool $paymentSuccess = false;
    public string $cardName = '';
    public string $cardNumber = '';
    public string $cardExpiry = '';
    public string $cardCvv = '';
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
        
        $this->reset('cardName', 'cardNumber', 'cardExpiry', 'cardCvv', 'paymentSuccess', 'paymentReference');
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
     * Process simulated payment and activate membership.
     */
    public function processPurchase()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'cardName' => 'required|string|max:100',
            'cardNumber' => 'required|string|min:12',
            'cardExpiry' => 'required|string|regex:/^\d{2}\/\d{2}$/',
            'cardCvv' => 'required|string|min:3|max:4',
        ]);

        try {
            if (!$this->globalSetting) {
                throw new Exception("Membership configuration is missing.");
            }

            $purchaseService = app(MembershipPurchaseService::class);
            $result = $purchaseService->purchase(Auth::user(), $this->globalSetting, [
                'name' => $this->cardName,
                'number' => $this->cardNumber,
                'expiry' => $this->cardExpiry,
                'cvv' => $this->cardCvv,
            ]);

            if ($result['success']) {
                $this->paymentSuccess = true;
                $this->paymentReference = $result['reference'];
                $this->isMember = true;
                $this->dispatch('membership-activated');
            }
        } catch (Exception $e) {
            $this->addError('payment', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.public.upgrade-modal');
    }
}
