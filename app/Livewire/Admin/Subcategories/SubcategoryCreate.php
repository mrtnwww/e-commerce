<?php

namespace App\Livewire\Admin\Subcategories;

use App\Models\Category;
use App\Models\Family;
use App\Models\Subcategory;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SubcategoryCreate extends Component
{
    public $families;
    public $subcategory = [
        'family_id' => '',
        'category_id' => '',
        'name' => ''
    ];

    // Consultar familias al montarse el componente
    public function mount()
    {
        $this->families = Family::all();
    }

    // Actualizar el valor de $subcategory[category_id] cada vez que cambia el valor de $subcategory[family_id]
    public function updatedSubcategoryFamilyId()
    {
        $this->subcategory['category_id'] = '';
    }

    // Propiedad computada ejecutada cada vez que cambia el valor de $subcategory[family_id]
    #[Computed()]
    public function categories()
    {
        return Category::where('family_id', $this->subcategory['family_id'])->get();
    }

    public function save()
    {
        $this->validate([
            'subcategory.family_id'   => 'required|exists:families,id',
            'subcategory.category_id' => 'required|exists:categories,id',
            'subcategory.name'        => 'required|string'
        ], [], [
            'subcategory.family_id'   => 'familia',
            'subcategory.category_id' => 'categoría',
            'subcategory.name'        => 'nombre'
        ]);

        Subcategory::create($this->subcategory);

        session()->flash('swal', [
            'title' => 'Exito',
            'text' => 'Subcategoría creada exitosamente',
            'icon' => 'success'
        ]);

        return redirect()->route('admin.subcategories.index');
    }

    public function render()
    {
        return view('livewire.admin.subcategories.subcategory-create');
    }
}
