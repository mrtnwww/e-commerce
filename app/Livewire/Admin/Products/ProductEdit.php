<?php

namespace App\Livewire\Admin\Products;

use App\Models\Category;
use App\Models\Family;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductEdit extends Component
{
    use WithFileUploads;

    // Recibir product de la vista
    public $product;

    public $productEdit;
    public $families;
    public $image;

    public $family_id = '';
    public $category_id = '';

    public function mount($product) {
        $this->productEdit = $product->only('sku', 'name', 'description', 'image_path', 'price', 'subcategory_id');

        $this->families = Family::all();

        $this->family_id = $product->subcategory->category->family->id;
        $this->category_id = $product->subcategory->category->id;
    }

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
        $this->productEdit['subcategory_id'] = '';
    }

    public function updatedCategoryId() {
        $this->productEdit['subcategory_id'] = '';
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
            'image' => 'nullable|image|max:1024',
            'productEdit.sku' => 'required|unique:products,sku,' . $this->product->id,
            'productEdit.name' => 'required|max:255',
            'productEdit.description'=> 'nullable',
            'productEdit.subcategory_id' => 'required|exists:subcategories,id',
            'productEdit.price' => 'required'
        ], [
            'productEdit.sku' => 'El campo código es obligatorío',
            'productEdit.name' => 'El campo nombre es obligatorío',
            'productEdit.description' => 'El campo descripción es obligatorío',
            'productEdit.subcategory_id' => 'El campo subcategoría es obligatorío',
            'productEdit.price' => 'El campo precio es obligatorío'
        ]);

        if ($this->image) {
            Storage::delete($this->productEdit['image_path']);

            $this->productEdit['image_path'] = $this->image->store('products');
        }

        $this->product->update($this->productEdit);

        session()->flash('swal', [
            'title' => 'Exito',
            'text' => 'Producto actualizado correctamente',
            'icon' => 'success'
        ]);

        return redirect()->route('admin.products.edit', $this->product);
    }

    public function render()
    {
        return view('livewire.admin.products.product-edit');
    }
}
