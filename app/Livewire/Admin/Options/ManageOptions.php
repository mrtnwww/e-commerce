<?php

namespace App\Livewire\Admin\Options;

use App\Models\Option;
use Livewire\Component;

class ManageOptions extends Component
{
    public $options;
    public $newOption = [
        'name' => '',
        'type' => '1',
        'features' => [
            [
                'value' => '',
                'description' => ''
            ]
        ]
    ];

    public $openModal = false;

    public function mount() {
        $this->options = Option::with('features')->get();
    }

    public function addFeature() {
        $this->newOption['features'][] = [
            'value' => '',
            'description' => ''
        ];
    }

    public function removeFeature($index) {
        unset($this->newOption['features'][$index]);
        $this->newOption['features'] = array_values($this->newOption['features']); // restablecer los indices
    }

    public function addOption() {
        $rules = [
            'newOption.name' => 'required',
            'newOption.type' => 'required|in:1,2',
            'newOption.features' => 'required|array|min:1',
        ];

        foreach($this->newOption['features'] as $index => $feature) {
            if ($this->newOption['type'] == 1) {
                $rule['newOption.features' . $index . 'value'] = 'required|string';
            } else {
                $rule['newOption.features' . $index . 'value'] = 'required|regex:/^#[a-f0-9]{6}$/i';
            }

            $rule['newOption.features' . $index . 'description'] = 'required|max:255';
        }

        $this->validate = $rules;

        $option = Option::create([
            'name' => $this->newOption['name'],
            'type' => $this->newOption['type']
        ]);

        foreach($this->newOption['features'] as $feature) {
            $option->features()->create([
                'value' => $feature['value'],
                'description' => $feature['description'],
                'option_id' => $option->id
            ]);
        }

        // Actualizar la vista con todas las opciones
        $this->options = Option::with('features')->get();

        // Cerrar modal y limpiar la variable newOption
        $this->reset('openModal', 'newOption');

        $this->dispatch('swal', [
            'title' => 'Bien hecho',
            'text' => 'Opciones añadidas',
            'icon' => 'success'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.options.manage-options');
    }
}
