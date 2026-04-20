<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Option;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options =  [
            [
                'name' => 'Talla',
                'type' => 1,
                'features' => [
                    [
                        'value' => 's',
                        'description' => 'small'
                    ],
                    [
                        'value' => 'm',
                        'description' => 'medium'
                    ],
                    [
                        'value' => 'l',
                        'description' => 'large'
                    ],
                    [
                        'value' => 'xl',
                        'description' => 'extra large'
                    ]
                ]
            ],
            [
                'name' => 'Color',
                'type' => 2,
                'features' => [
                    [
                        'value' => '#000000',
                        'description' => 'black'
                    ],
                    [
                        'value' => '#ffffff',
                        'description' => 'white'
                    ],
                    [
                        'value' => '#00ff00',
                        'description' => 'green'
                    ],
                    [
                        'value' => '#ff0000',
                        'description' => 'red'
                    ],[
                        'value' => '#0000ff',
                        'description' => 'blue'
                    ]
                ]
            ],
            [
                'name' => 'Sexo',
                'type' => 1,
                'features' => [
                    [
                        'value' => 'm',
                        'description' => 'male'
                    ],
                    [
                        'value' => 'f',
                        'description' => 'female'
                    ]
                ]
            ]
        ];

        foreach ($options as $option) {
            $optionModel = Option::create([
                'name' => $option['name'],
                'type' => $option['type']
            ]);

            foreach ($option['features'] as $feature) {
                $optionModel->features()->create([
                    'value' => $feature['value'],
                    'description' => $feature['description']
                ]);
            }
        }
    }
}
