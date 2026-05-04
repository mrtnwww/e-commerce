<?php

namespace App\Livewire\Admin;

use App\Models\Discount;
use Livewire\Component;
use Livewire\WithPagination;

class DiscountIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showForm = false;

    public bool $confirmDelete = false;

    public ?int $deleteId = null;

    public ?int $editId = null;

    // Form
    public string $code = '';

    public string $type = 'percentage';

    public string $value = '';

    public string $minimumOrder = '';

    public string $maxUses = '';

    public bool $active = true;

    public string $expiresAt = '';

    protected function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:discounts,code,'.($this->editId ?? 'NULL'),
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'minimumOrder' => 'nullable|numeric|min:0',
            'maxUses' => 'nullable|integer|min:1',
            'active' => 'boolean',
            'expiresAt' => 'nullable|date|after:today',
        ];
    }

    protected $messages = [
        'code.unique' => 'Este código ya existe.',
        'expiresAt.after' => 'La fecha de expiración debe ser futura.',
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
        $d = Discount::findOrFail($id);
        $this->editId = $d->id;
        $this->code = $d->code;
        $this->type = $d->type;
        $this->value = $d->value;
        $this->minimumOrder = $d->minimum_order ?? '';
        $this->maxUses = $d->max_uses ?? '';
        $this->active = $d->active;
        $this->expiresAt = $d->expires_at?->format('Y-m-d') ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'code' => strtoupper($this->code),
            'type' => $this->type,
            'value' => $this->value,
            'minimum_order' => $this->minimumOrder ?: null,
            'max_uses' => $this->maxUses ?: null,
            'active' => $this->active,
            'expires_at' => $this->expiresAt ?: null,
        ];

        if ($this->editId) {
            Discount::findOrFail($this->editId)->update($data);
            $msg = 'Descuento actualizado';
        } else {
            Discount::create($data);
            $msg = 'Descuento creado';
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
        Discount::findOrFail($this->deleteId)->delete();
        $this->confirmDelete = false;
        $this->deleteId = null;
        $this->dispatch('notify', message: 'Descuento eliminado');
    }

    public function toggleActive(int $id): void
    {
        $d = Discount::findOrFail($id);
        $d->update(['active' => ! $d->active]);
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->code = $this->value = $this->minimumOrder = $this->maxUses = $this->expiresAt = '';
        $this->type = 'percentage';
        $this->active = true;
        $this->resetValidation();
    }

    public function getDiscountsProperty()
    {
        return Discount::when($this->search, fn ($q) => $q->where('code', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.admin.discount-index', [
            'discounts' => $this->discounts,
        ])->layout('layouts.admin');
    }
}
