<?php

namespace App\Livewire\Admin\Catalog;

use App\Models\Family;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class FamilyIndex extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public bool $showForm = false;

    public bool $confirmDelete = false;

    public ?int $deleteId = null;

    public ?int $editId = null;

    public string $name = '';

    public string $description = '';

    public bool $active = true;

    public $image = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'active' => 'boolean',
        'image' => 'nullable|image|max:1024',
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
        $family = Family::findOrFail($id);
        $this->editId = $family->id;
        $this->name = $family->name;
        $this->description = $family->description ?? '';
        $this->active = $family->active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'active' => $this->active,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('families', 'public');
        }

        if ($this->editId) {
            Family::findOrFail($this->editId)->update($data);
            $msg = 'Familia actualizada';
        } else {
            Family::create($data);
            $msg = 'Familia creada';
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
        Family::findOrFail($this->deleteId)->delete();
        $this->confirmDelete = false;
        $this->deleteId = null;
        $this->dispatch('notify', message: 'Familia eliminada');
    }

    public function toggleActive(int $id): void
    {
        $family = Family::findOrFail($id);
        $family->update(['active' => ! $family->active]);
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->name = '';
        $this->description = '';
        $this->active = true;
        $this->image = null;
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showForm = false;
    }

    public function getFamiliesProperty()
    {
        return Family::withCount('categories')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('order')
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.admin.catalog.family-index', [
            'families' => $this->families,
        ])->layout('layouts.admin');
    }
}
