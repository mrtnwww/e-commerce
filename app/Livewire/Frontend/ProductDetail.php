<?php

namespace App\Livewire\Frontend;

use App\Models\Product;
use Livewire\Component;

class ProductDetail extends Component
{
    public Product $product;

    public int $quantity = 1;

    public int $activeImage = 0;

    public bool $addedToCart = false;

    public function mount(string $slug): void
    {
        $this->product = Product::active()
            ->with('subcategory.category.family')
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function setImage(int $index): void
    {
        $this->activeImage = $index;
    }

    public function incrementQty(): void
    {
        if ($this->quantity < $this->product->stock) {
            $this->quantity++;
        }
    }

    public function decrementQty(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(): void
    {
        if ($this->product->is_out_of_stock) {
            return;
        }

        $this->dispatch('cart-add', productId: $this->product->id);

        $this->addedToCart = true;

        // Reset after 2 seconds via JS
        $this->js("setTimeout(() => \$wire.set('addedToCart', false), 2000)");
    }

    public function getRelatedProductsProperty()
    {
        return Product::active()
            ->where('subcategory_id', $this->product->subcategory_id)
            ->where('id', '!=', $this->product->id)
            ->limit(4)
            ->get();
    }

    public function render()
    {
        return view('livewire.frontend.product-detail', [
            'relatedProducts' => $this->relatedProducts,
        ])->layout('layouts.store', [
            'pageTitle' => $this->product->name,
            'metaDescription' => $this->product->short_description,
        ]);
    }
}
