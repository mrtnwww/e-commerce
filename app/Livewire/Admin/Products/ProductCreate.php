<?php

namespace App\Livewire\Admin\Products;

use App\Models\Category;
use App\Models\Family;
use App\Models\Product;
use App\Models\Subcategory;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductCreate extends Component
{
    use WithFileUploads;

    public $families;
    public $family_id = '';
    public $category_id = '';
    public $image = '';

    public $product = [
        'sku' => '',
        'name' => '',
        'description'=> '',
        'image_path' => '',
        'subcategory_id' => '',
        'price' => ''
    ];

    // Se ejecuta cada vez que se monta el componente
    public function mount() {
        $this->families = Family::all();
    }

    // Se ejecuta cada vez que se renderiza la vista
    public function boot()
    {
        $this->withValidator(function($validator) {
            if ($validator->fails()) {
                $this->dispatch('swal', [
                    'title' => 'Error',
                    'text' => 'El formulario contiene errores',
                    'icon' => 'error',
                ]);
            }
        });
    }

    public function updatedFamilyId() {
        $this->category_id = '';
        $this->product['subcategory_id'] = '';
    }

    public function updatedCategoryId() {
        $this->product['subcategory_id'] = '';
    }

    #[Computed()]
    public function categories() {
        return Category::where('family_id', $this->family_id)->get();
    }
    #[Computed()]
    public function subcategories() {
        return Subcategory::where('category_id', $this->category_id)->get();
    }

    public function store() {
        $this->validate([
            'image' => 'required|image|max:1024',
            'product.sku' => 'required|unique:products,sku',
            'product.name' => 'required|max:255',
            'product.description'=> 'nullable',
            'product.subcategory_id' => 'required|exists:subcategories,id',
            'product.price' => 'required'
        ], [
            'product.sku' => 'El campo código es obligatorío',
            'product.name' => 'El campo nombre es obligatorío',
            'product.description' => 'El campo descripción es obligatorío',
            'product.subcategory_id' => 'El campo subcategoría es obligatorío',
            'product.price' => 'El campo precio es obligatorío'
        ]);

        // Guardar la foto en la ruta /storage/app/products y retornar la ruta
        $this->product['image_path'] = $this->image->store('products');

        $product = Product::create($this->product);

        session()->flash('swal', [
            'title' => 'Exito',
            'text' => 'Producto creado correctamente',
            'icon' => 'success'
        ]);

        return redirect()->route('admin.products.edit', $product);
    }

    public function render()
    {
        return view('livewire.admin.products.product-create');
    }
}
