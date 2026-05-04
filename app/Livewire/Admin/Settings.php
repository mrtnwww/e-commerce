<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;

class Settings extends Component
{
    use WithFileUploads;

    // General
    public string $storeName = '';

    public string $storeEmail = '';

    public string $storePhone = '';

    public string $storeAddress = '';

    public string $storeCurrency = 'COP';

    // Shipping
    public string $shippingCost = '0';

    public string $freeShippingMin = '';

    public bool $freeShipping = false;

    // Social
    public string $instagram = '';

    public string $facebook = '';

    public string $whatsapp = '';

    // Logo
    public $logo = null;

    public ?string $currentLogo = null;

    public function mount(): void
    {
        $settings = config('store', []);
        $this->storeName = $settings['name'] ?? config('app.name');
        $this->storeEmail = $settings['email'] ?? '';
        $this->storePhone = $settings['phone'] ?? '';
        $this->storeAddress = $settings['address'] ?? '';
        $this->storeCurrency = $settings['currency'] ?? 'COP';
        $this->shippingCost = $settings['shipping_cost'] ?? '0';
        $this->freeShippingMin = $settings['free_shipping_min'] ?? '';
        $this->freeShipping = $settings['free_shipping'] ?? false;
        $this->instagram = $settings['instagram'] ?? '';
        $this->facebook = $settings['facebook'] ?? '';
        $this->whatsapp = $settings['whatsapp'] ?? '';
        $this->currentLogo = $settings['logo'] ?? null;
    }

    protected $rules = [
        'storeName' => 'required|string|max:255',
        'storeEmail' => 'required|email',
        'storePhone' => 'nullable|string|max:20',
        'storeAddress' => 'nullable|string|max:500',
        'storeCurrency' => 'required|string|max:10',
        'shippingCost' => 'required|numeric|min:0',
        'freeShippingMin' => 'nullable|numeric|min:0',
        'instagram' => 'nullable|url',
        'facebook' => 'nullable|url',
        'whatsapp' => 'nullable|string|max:20',
        'logo' => 'nullable|image|max:1024',
    ];

    public function save(): void
    {
        $this->validate();

        $logoPath = $this->currentLogo;
        if ($this->logo) {
            $logoPath = $this->logo->store('store', 'public');
        }

        $settings = [
            'name' => $this->storeName,
            'email' => $this->storeEmail,
            'phone' => $this->storePhone,
            'address' => $this->storeAddress,
            'currency' => $this->storeCurrency,
            'shipping_cost' => $this->shippingCost,
            'free_shipping_min' => $this->freeShippingMin ?: null,
            'free_shipping' => $this->freeShipping,
            'instagram' => $this->instagram,
            'facebook' => $this->facebook,
            'whatsapp' => $this->whatsapp,
            'logo' => $logoPath,
        ];

        // Persist to config/store.php
        $export = "<?php\nreturn ".var_export($settings, true).";\n";
        file_put_contents(config_path('store.php'), $export);

        $this->currentLogo = $logoPath;
        $this->logo = null;

        $this->dispatch('notify', message: 'Configuración guardada correctamente');
    }

    public function render()
    {
        return view('livewire.admin.settings')
            ->layout('layouts.admin');
    }
}
