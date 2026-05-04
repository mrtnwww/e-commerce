<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'code', 'type', 'value',
        'minimum_order', 'max_uses', 'used_count',
        'active', 'expires_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'value' => 'decimal:2',
        'minimum_order' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    public function isValid(): bool
    {
        if (! $this->active) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->minimum_order && $subtotal < $this->minimum_order) {
            return 0;
        }

        return $this->type === 'percentage'
            ? round($subtotal * ($this->value / 100), 2)
            : min($this->value, $subtotal);
    }
}
