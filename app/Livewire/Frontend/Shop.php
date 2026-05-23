<?php

namespace App\Livewire\Frontend;

use App\Models\Family;
use App\Models\Product;
use App\Services\CartService;
use Livewire\Component;
use Livewire\WithPagination;

class Shop extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortBy = 'featured';

    public ?int $familyId = null;

    public ?int $categoryId = null;

    public ?int $subcategoryId = null;

    public string $priceMin = '';

    public string $priceMax = '';

    public bool $onlyInStock = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'familyId' => ['except' => '', 'as' => 'familia'],
        'categoryId' => ['except' => '', 'as' => 'categoria'],
        'subcategoryId' => ['except' => '', 'as' => 'sub'],
        'sortBy' => ['except' => 'featured'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFamilyId(): void
    {
        $this->categoryId = null;
        $this->subcategoryId = null;
        $this->resetPage();
    }

    public function updatingCategoryId(): void
    {
        $this->subcategoryId = null;
        $this->resetPage();
    }

    public function updatingSubcategoryId(): void
    {
        $this->resetPage();
    }

    public function addToCart(int $productId): void
    {
        $success = (new CartService)->add($productId);

        if ($success) {
            $this->dispatch('cart-add');
            $this->dispatch('notify', message: 'Producto añadido al carrito');
        }
    }

    public function getProductsProperty()
    {
        return Product::active()
            ->with('subcategory.category.family')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%"))
            ->when($this->subcategoryId, fn ($q) => $q->where('subcategory_id', $this->subcategoryId))
            ->when($this->categoryId && ! $this->subcategoryId, fn ($q) => $q->whereHas('subcategory', fn ($s) => $s->where('category_id', $this->categoryId)))
            ->when($this->familyId && ! $this->categoryId, fn ($q) => $q->whereHas('subcategory.category', fn ($s) => $s->where('family_id', $this->familyId)))
            ->when($this->priceMin !== '', fn ($q) => $q->where('price', '>=', $this->priceMin))
            ->when($this->priceMax !== '', fn ($q) => $q->where('price', '<=', $this->priceMax))
            ->when($this->onlyInStock, fn ($q) => $q->where('stock', '>', 0))
            ->when($this->sortBy === 'featured', fn ($q) => $q->orderBy('featured', 'desc')->orderBy('order'))
            ->when($this->sortBy === 'price_asc', fn ($q) => $q->orderBy('price'))
            ->when($this->sortBy === 'price_desc', fn ($q) => $q->orderByDesc('price'))
            ->when($this->sortBy === 'newest', fn ($q) => $q->latest())
            ->paginate(16);
    }

    public function getFamiliesProperty()
    {
        return Family::active()->with(['categories' => fn ($q) => $q->active()])->orderBy('order')->get();
    }

    public function render()
    {
        return view('livewire.frontend.shop', [
            'products' => $this->products,
            'families' => $this->families,
        ])->layout('layouts.store');
    }
}
