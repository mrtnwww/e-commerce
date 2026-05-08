<?php

namespace App\Livewire\Admin\Catalog;

use App\Models\Category;
use App\Models\Subcategory;
use Livewire\Component;
use Livewire\WithPagination;

class SubcategoryIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $categoryFilter = '';

    public bool $showForm = false;

    public bool $confirmDelete = false;

    public ?int $deleteId = null;

    public ?int $editId = null;

    // Form
    public string $name = '';

    public string $description = '';

    public bool $active = true;

    public ?int $categoryId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'categoryId' => 'required|exists:categories,id',
        'description' => 'nullable|string',
        'active' => 'boolean',
    ];

    protected $messages = [
        'categoryId.required' => 'Selecciona una categoría.',
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
        $sub = Subcategory::findOrFail($id);
        $this->editId = $sub->id;
        $this->name = $sub->name;
        $this->description = $sub->description ?? '';
        $this->active = $sub->active;
        $this->categoryId = $sub->category_id;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'category_id' => $this->categoryId,
            'description' => $this->description,
            'active' => $this->active,
        ];

        if ($this->editId) {
            Subcategory::findOrFail($this->editId)->update($data);
            $msg = 'Subcategoría actualizada';
        } else {
            Subcategory::create($data);
            $msg = 'Subcategoría creada';
        }

        $this->showForm = false;
        $this->resetForm();
        $this->dispatch('notify', message: $msg);
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->confirmDelete = true;
    }

    public function delete(): void
    {
        Subcategory::findOrFail($this->deleteId)->delete();
        $this->confirmDelete = false;
        $this->deleteId = null;
        $this->dispatch('notify', message: 'Subcategoría eliminada');
    }

    public function toggleActive(int $id): void
    {
        $sub = Subcategory::findOrFail($id);
        $sub->update(['active' => ! $sub->active]);
    }

    private function resetForm(): void
    {
        $this->editId = $this->categoryId = null;
        $this->name = $this->description = '';
        $this->active = true;
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showForm = false;
    }

    public function getSubcategoriesProperty()
    {
        return Subcategory::with('category.family')
            ->withCount('products')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->categoryFilter, fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->orderBy('order')
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.admin.catalog.subcategory-index', [
            'subcategories' => $this->subcategories,
            'categories' => Category::with('family')->orderBy('name')->get(),
        ])->layout('layouts.admin');
    }
}
