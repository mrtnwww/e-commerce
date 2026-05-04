<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'number', 'status', 'customer_name', 'customer_email',
        'customer_phone', 'shipping_address', 'shipping_city',
        'shipping_department', 'shipping_zip', 'subtotal', 'shipping_cost',
        'discount', 'total', 'payment_method', 'payment_status',
        'notes', 'paid_at', 'shipped_at', 'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    const STATUSES = [
        'pending' => ['label' => 'Pendiente',   'color' => 'amber'],
        'processing' => ['label' => 'En proceso',  'color' => 'blue'],
        'shipped' => ['label' => 'Enviado',      'color' => 'purple'],
        'delivered' => ['label' => 'Entregado',   'color' => 'green'],
        'cancelled' => ['label' => 'Cancelado',   'color' => 'red'],
        'refunded' => ['label' => 'Reembolsado', 'color' => 'gray'],
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($order) {
            $order->number = 'ORD-'.str_pad(
                (Order::max('id') ?? 0) + 1,
                5, '0', STR_PAD_LEFT
            );
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status]['label'] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUSES[$this->status]['color'] ?? 'gray';
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
