<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortBy = 'created_at';

    public string $sortDir = 'desc';

    public ?User $selectedCustomer = null;

    public bool $showModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $column): void
    {
        $this->sortDir = $this->sortBy === $column && $this->sortDir === 'asc' ? 'desc' : 'asc';
        $this->sortBy = $column;
    }

    public function viewCustomer(int $id): void
    {
        $this->selectedCustomer = User::withCount('orders')
            ->with(['orders' => fn ($q) => $q->latest()->limit(5)])
            ->findOrFail($id);
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedCustomer = null;
    }

    public function getCustomersProperty()
    {
        return User::where('is_admin', false)
            ->withCount('orders')
            ->withSum(['orders' => fn ($q) => $q->whereNotIn('status', ['cancelled', 'refunded'])], 'total')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%"))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.admin.customer-index', [
            'customers' => $this->customers,
        ])->layout('layouts.admin');
    }
}
