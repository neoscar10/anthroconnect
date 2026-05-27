<?php

namespace App\Models;

use App\Enums\Payment\PaymentGateway;
use App\Enums\Payment\PaymentPurpose;
use App\Enums\Payment\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'gateway',
        'purpose',
        'reference',
        'gateway_reference',
        'gateway_order_id',
        'gateway_payment_id',
        'gateway_signature',
        'amount',
        'currency',
        'status',
        'failure_reason',
        'meta',
        'paid_at',
        'failed_at',
        'refunded_at',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
        'gateway' => PaymentGateway::class,
        'purpose' => PaymentPurpose::class,
        'meta' => 'array',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the user who made the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the transaction is captured.
     */
    public function isCaptured(): bool
    {
        return $this->status === PaymentStatus::CAPTURED;
    }

    /**
     * Check if the transaction is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === PaymentStatus::FAILED;
    }

    /**
     * Check if the transaction is pending.
     */
    public function isPending(): bool
    {
        return $this->status === PaymentStatus::PENDING;
    }

    /**
     * Check if the transaction is authorized.
     */
    public function isAuthorized(): bool
    {
        return $this->status === PaymentStatus::AUTHORIZED;
    }

    /**
     * Determine if a transition to the target status is allowed.
     */
    public function canTransitionTo(PaymentStatus $target): bool
    {
        $current = $this->status;

        switch ($current) {
            case PaymentStatus::INITIATED:
                return in_array($target, [
                    PaymentStatus::PENDING,
                    PaymentStatus::CAPTURED,
                    PaymentStatus::FAILED,
                ]);

            case PaymentStatus::PENDING:
                return in_array($target, [
                    PaymentStatus::AUTHORIZED,
                    PaymentStatus::CAPTURED,
                    PaymentStatus::FAILED,
                ]);

            case PaymentStatus::AUTHORIZED:
                return in_array($target, [
                    PaymentStatus::CAPTURED,
                    PaymentStatus::FAILED,
                ]);

            case PaymentStatus::CAPTURED:
                return $target === PaymentStatus::REFUNDED;

            case PaymentStatus::FAILED:
                return in_array($target, [
                    PaymentStatus::PENDING,
                    PaymentStatus::CAPTURED,
                ]);

            case PaymentStatus::REFUNDED:
                return false;
        }

        return false;
    }

    /**
     * Transition the transaction to the target status.
     */
    public function transitionTo(PaymentStatus $target): void
    {
        if (!$this->canTransitionTo($target)) {
            throw new \Exception("Cannot transition transaction [{$this->reference}] from status [{$this->status->value}] to [{$target->value}].");
        }

        $this->status = $target;
        
        if ($target === PaymentStatus::CAPTURED) {
            $this->paid_at = now();
        } elseif ($target === PaymentStatus::FAILED) {
            $this->failed_at = now();
        } elseif ($target === PaymentStatus::REFUNDED) {
            $this->refunded_at = now();
        }

        $this->save();
    }

    /**
     * Determine if the transaction can be transitioned to captured.
     */
    public function canBeCaptured(): bool
    {
        return $this->canTransitionTo(PaymentStatus::CAPTURED);
    }
}
