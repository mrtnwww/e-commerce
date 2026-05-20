<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortDir = 'desc';
    public string $sortBy = 'created_at';

    public ?User $selectedCustomer = null;
    public bool $showModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    // Hook de busqueda
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // Manejo de ordenamiento
    public function handleSortBy(string $column): void
    {
        $this->sortDir = $this->sortBy === $column && $this->sortDir === 'asc' ? 'desc' : 'asc';
        $this->sortBy = $column;
    }

    // Mostrar modal con detalle de cliente
    public function viewCustomer(int $id): void
    {
        $this->selectedCustomer = User::withCount('orders')
            ->with(['orders' => fn ($q) => $q->latest()->limit(5)])
            ->findOrFail($id);
        $this->showModal = true;
    }

    // Cerrar modal detalle de cliente
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedCustomer = null;
    }

    // Lista de clientes
    public function getCustomersProperty()
    {
        return User::where('is_admin', false) // Se excluye del listado al usuario administrador
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
