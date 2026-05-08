<?php

namespace App\Livewire\Admin\Catalog;

use App\Models\Category;
use App\Models\Family;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class CategoryIndex extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public string $familyFilter = '';

    public bool $showForm = false;

    public bool $confirmDelete = false;

    public ?int $deleteId = null;

    public ?int $editId = null;

    // Form
    public string $name = '';

    public string $description = '';

    public bool $active = true;

    public ?int $familyId = null;

    public $image = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'familyId' => 'required|exists:families,id',
        'description' => 'nullable|string',
        'active' => 'boolean',
        'image' => 'nullable|image|max:1024',
    ];

    protected $messages = [
        'familyId.required' => 'Selecciona una familia.',
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
        $cat = Category::findOrFail($id);
        $this->editId = $cat->id;
        $this->name = $cat->name;
        $this->description = $cat->description ?? '';
        $this->active = $cat->active;
        $this->familyId = $cat->family_id;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'family_id' => $this->familyId,
            'description' => $this->description,
            'active' => $this->active,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('categories', 'public');
        }

        if ($this->editId) {
            Category::findOrFail($this->editId)->update($data);
            $msg = 'Categoría actualizada';
        } else {
            Category::create($data);
            $msg = 'Categoría creada';
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
        Category::findOrFail($this->deleteId)->delete();
        $this->confirmDelete = false;
        $this->deleteId = null;
        $this->dispatch('notify', message: 'Categoría eliminada');
    }

    public function toggleActive(int $id): void
    {
        $cat = Category::findOrFail($id);
        $cat->update(['active' => ! $cat->active]);
    }

    private function resetForm(): void
    {
        $this->editId = $this->familyId = null;
        $this->name = $this->description = '';
        $this->active = true;
        $this->image = null;
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showForm = false;
    }

    public function getCategoriesProperty()
    {
        return Category::with('family')
            ->withCount('subcategories')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->familyFilter, fn ($q) => $q->where('family_id', $this->familyFilter))
            ->orderBy('order')
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.admin.catalog.category-index', [
            'categories' => $this->categories,
            'families' => Family::orderBy('name')->get(),
        ])->layout('layouts.admin');
    }
}
