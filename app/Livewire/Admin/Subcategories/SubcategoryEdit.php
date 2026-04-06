<?php

namespace App\Livewire\Admin\Subcategories;

use App\Models\Category;
use App\Models\Family;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SubcategoryEdit extends Component
{
    public $subcategoryEdit;
    public $subcategory;
    public $families;

    public function mount($subcategory)
    {
        $this->families = Family::all();

        $this->subcategoryEdit = [
            'family_id' => $subcategory->category->family_id,
            'category_id' => $subcategory->category_id,
            'name' => $subcategory->name
        ];
    }

    public function updatedSubcategoryEditFamilyId()
    {
        $this->subcategoryEdit['category_id'] = '';
    }

    #[Computed()]
    public function categories()
    {
        return Category::where('family_id', $this->subcategoryEdit['family_id'])->get();
    }

    public function save()
    {
        $this->validate([
            'subcategoryEdit.family_id' => 'required|exists:families,id',
            'subcategoryEdit.category_id' => 'required|exists:categories,id',
            'subcategoryEdit.name' => 'required|string'
        ], [], [
            'subcategoryEdit.family_id' => 'familia',
            'subcategoryEdit.category_id' => 'categoría',
            'subcategoryEdit.name' => 'nombre'
        ]);

        $this->subcategory->update($this->subcategoryEdit);

        // Disparar evento de Livewire, capturado en el layout mediante Livewire.on('swal')
        $this->dispatch('swal', [
            'title' => 'Exito',
            'text' => 'Subcategoría actualizada correctamente',
            'icon' => 'success'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.subcategories.subcategory-edit');
    }
}
