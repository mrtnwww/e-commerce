<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\Subcategory;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ProductIndex extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public string $subcategory = '';

    public string $stockFilter = '';

    public string $sortBy = 'created_at';

    public string $sortDir = 'desc';

    public int $perPage = 15;

    public bool $showForm = false;

    public bool $confirmDelete = false;

    public ?int $deleteId = null;

    // Form fields
    public ?int $editId = null;

    public string $name = '';

    public string $description = '';

    public string $shortDescription = '';

    public string $price = '';

    public string $comparePrice = '';

    public int $stock = 0;

    public int $lowStockThreshold = 5;

    public string $sku = '';

    public ?int $subcategoryId = null;

    public bool $active = true;

    public bool $featured = false;

    public array $uploadedImages = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'subcategory' => ['except' => ''],
        'stockFilter' => ['except' => ''],
    ];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'comparePrice' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'lowStockThreshold' => 'required|integer|min:1',
            'sku' => 'nullable|string|max:100|unique:products,sku,'.($this->editId ?? 'NULL'),
            'subcategoryId' => 'nullable|exists:subcategories,id',
            'description' => 'nullable|string',
            'shortDescription' => 'nullable|string|max:500',
            'active' => 'boolean',
            'featured' => 'boolean',
            'uploadedImages.*' => 'nullable|image|max:2048',
        ];
    }

    protected $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'price.required' => 'El precio es obligatorio.',
        'price.numeric' => 'El precio debe ser un número.',
        'stock.required' => 'El stock es obligatorio.',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $product = Product::findOrFail($id);
        $this->editId = $product->id;
        $this->name = $product->name;
        $this->description = $product->description ?? '';
        $this->shortDescription = $product->short_description ?? '';
        $this->price = $product->price;
        $this->comparePrice = $product->compare_price ?? '';
        $this->stock = $product->stock;
        $this->lowStockThreshold = $product->low_stock_threshold;
        $this->sku = $product->sku ?? '';
        $this->subcategoryId = $product->subcategory_id;
        $this->active = $product->active;
        $this->featured = $product->featured;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $images = [];
        foreach ($this->uploadedImages as $img) {
            $images[] = $img->store('products', 'public');
        }

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'short_description' => $this->shortDescription,
            'price' => $this->price,
            'compare_price' => $this->comparePrice ?: null,
            'stock' => $this->stock,
            'low_stock_threshold' => $this->lowStockThreshold,
            'sku' => $this->sku ?: null,
            'subcategory_id' => $this->subcategoryId,
            'active' => $this->active,
            'featured' => $this->featured,
        ];

        if ($this->editId) {
            $product = Product::findOrFail($this->editId);
            if (!empty($images)) {
                $data['images'] = array_merge($product->images ?? [], $images);
            }
            $product->update($data);
            $message = 'Producto actualizado correctamente';
        } else {
            $data['images'] = $images;
            Product::create($data);
            $message = 'Producto creado correctamente';
        }

        $this->showForm = false;
        $this->resetForm();
        $this->dispatch('notify', message: $message);
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->confirmDelete = true;
    }

    public function delete(): void
    {
        Product::findOrFail($this->deleteId)->delete();
        $this->confirmDelete = false;
        $this->deleteId = null;
        $this->dispatch('notify', message: 'Producto eliminado');
    }

    public function toggleActive(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->update(['active' => ! $product->active]);
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->name = '';
        $this->description = '';
        $this->shortDescription = '';
        $this->price = '';
        $this->comparePrice = '';
        $this->stock = 0;
        $this->lowStockThreshold = 5;
        $this->sku = '';
        $this->subcategoryId = null;
        $this->active = true;
        $this->featured = false;
        $this->uploadedImages = [];
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showForm = false;
    }

    public function getProductsProperty()
    {
        return Product::with('subcategory.category')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('sku', 'like', "%{$this->search}%"))
            ->when($this->subcategory, fn ($q) => $q->where('subcategory_id', $this->subcategory))
            ->when($this->stockFilter === 'low', fn ($q) => $q->lowStock())
            ->when($this->stockFilter === 'out', fn ($q) => $q->outOfStock())
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin.products.product-index', [
            'products' => $this->products,
            'subcategories' => Subcategory::with('category')->orderBy('name')->get(),
        ])->layout('layouts.admin');
    }
}
