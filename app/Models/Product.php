<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'subcategory_id', 'name', 'slug', 'description', 'short_description',
        'price', 'compare_price', 'stock', 'low_stock_threshold',
        'sku', 'barcode', 'weight', 'images', 'active', 'featured', 'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'images' => 'array',
        'active' => 'boolean',
        'featured' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn ($m) => $m->slug ??= Str::slug($m->name));
        static::updating(fn ($m) => $m->slug = Str::slug($m->name));
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function getMainImageAttribute(): string
    {
        return ($this->images[0] ?? null)
            ? asset('storage/'.$this->images[0])
            : asset('images/no-image.png');
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return round((1 - $this->price / $this->compare_price) * 100);
        }

        return null;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock > 0 && $this->stock <= $this->low_stock_threshold;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->stock <= 0;
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock > 0 AND stock <= low_stock_threshold');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock', '<=', 0);
    }
}
