<?php

namespace App\Livewire\Admin;

use App\Models\Banner;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class BannerIndex extends Component
{
    use WithFileUploads, WithPagination;

    public bool $showForm = false;

    public bool $confirmDelete = false;

    public ?int $deleteId = null;

    public ?int $editId = null;

    // Form
    public string $title = '';

    public string $subtitle = '';

    public string $buttonText = '';

    public string $buttonUrl = '';

    public bool $active = true;

    public int $order = 0;

    public $image = null;

    public ?string $existingImage = null;

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'buttonText' => 'nullable|string|max:100',
            'buttonUrl' => 'nullable|url',
            'active' => 'boolean',
            'order' => 'integer|min:0',
            'image' => $this->editId ? 'nullable|image|max:2048' : 'required|image|max:2048',
        ];
    }

    protected $messages = [
        'image.required' => 'La imagen es obligatoria.',
        'buttonUrl.url' => 'El enlace debe ser una URL válida.',
    ];

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $b = Banner::findOrFail($id);
        $this->editId = $b->id;
        $this->title = $b->title;
        $this->subtitle = $b->subtitle ?? '';
        $this->buttonText = $b->button_text ?? '';
        $this->buttonUrl = $b->button_url ?? '';
        $this->active = $b->active;
        $this->order = $b->order;
        $this->existingImage = $b->image;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'subtitle' => $this->subtitle ?: null,
            'button_text' => $this->buttonText ?: null,
            'button_url' => $this->buttonUrl ?: null,
            'active' => $this->active,
            'order' => $this->order,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('banners', 'public');
        } elseif ($this->editId) {
            $data['image'] = $this->existingImage;
        }

        if ($this->editId) {
            Banner::findOrFail($this->editId)->update($data);
            $msg = 'Banner actualizado';
        } else {
            Banner::create($data);
            $msg = 'Banner creado';
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
        Banner::findOrFail($this->deleteId)->delete();
        $this->confirmDelete = false;
        $this->deleteId = null;
        $this->dispatch('notify', message: 'Banner eliminado');
    }

    public function toggleActive(int $id): void
    {
        $b = Banner::findOrFail($id);
        $b->update(['active' => ! $b->active]);
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->title = $this->subtitle = $this->buttonText = $this->buttonUrl = '';
        $this->existingImage = null;
        $this->image = null;
        $this->active = true;
        $this->order = 0;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.banner-index', [
            'banners' => Banner::orderBy('order')->paginate(10),
        ])->layout('layouts.admin');
    }
}
