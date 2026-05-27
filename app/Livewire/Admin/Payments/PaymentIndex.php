<?php

namespace App\Livewire\Admin\Payments;

use App\Models\PaymentTransaction;
use App\Models\PaymentWebhookLog;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = 'all';
    public $gateway = 'all';
    public $purpose = 'all';
    public $dateFrom = '';
    public $dateTo = '';

    public $selectedTransactionId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'all'],
        'gateway' => ['except' => 'all'],
        'purpose' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingGateway()
    {
        $this->resetPage();
    }

    public function updatingPurpose()
    {
        $this->resetPage();
    }

    public function viewDetails($id)
    {
        $this->selectedTransactionId = $id;
    }

    public function closeDetails()
    {
        $this->selectedTransactionId = null;
    }

    public function render()
    {
        $query = PaymentTransaction::query()->with('user');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('reference', 'like', '%' . $this->search . '%')
                  ->orWhere('gateway_order_id', 'like', '%' . $this->search . '%')
                  ->orWhere('gateway_payment_id', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($uq) {
                      $uq->where('name', 'like', '%' . $this->search . '%')
                         ->orWhere('email', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if ($this->gateway !== 'all') {
            $query->where('gateway', $this->gateway);
        }

        if ($this->purpose !== 'all') {
            $query->where('purpose', $this->purpose);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $transactions = $query->latest()->paginate(15);

        // Resolve details if modal is open
        $selectedTransaction = null;
        $webhookLogs = collect();
        if ($this->selectedTransactionId) {
            $selectedTransaction = PaymentTransaction::with('user')->find($this->selectedTransactionId);
            if ($selectedTransaction) {
                $webhookLogs = PaymentWebhookLog::where('transaction_reference', $selectedTransaction->reference)
                    ->orWhere('payload->payload->payment->entity->order_id', $selectedTransaction->gateway_order_id)
                    ->latest()
                    ->get();
            }
        }

        return view('livewire.admin.payments.payment-index', [
            'transactions' => $transactions,
            'selectedTransaction' => $selectedTransaction,
            'webhookLogs' => $webhookLogs,
        ])->layout('layouts.admin');
    }
}
