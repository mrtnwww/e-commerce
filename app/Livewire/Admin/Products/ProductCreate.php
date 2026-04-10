<?php

namespace App\Livewire\Admin\Products;

use App\Models\Category;
use App\Models\Family;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ProductCreate extends Component
{
    public $families;
    public $family_id = '';

    public $product = [
        'sku' => '',
        'name' => '',
        'description'=> '',
        'image_path' => '',
        'subcategory_id' => '',
        'price' => ''
    ];

    public function mount() {
        $this->families = Family::all();
    }

    #[Computed()]
    public function categories() {
        return Category::where('family_id', $this->family_id)->get();
    }

    public function render()
    {
        return view('livewire.admin.products.product-create');
    }
}
