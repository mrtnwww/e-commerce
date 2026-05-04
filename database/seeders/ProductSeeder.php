<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Family;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // ──────────────────────────────────────────────
        // FAMILIAS → CATEGORÍAS → SUBCATEGORÍAS → PRODUCTOS
        // ──────────────────────────────────────────────

        $catalog = [
            [
                'name' => 'Ropa',
                'description' => 'Prendas de vestir para hombre, mujer y niños',
                'categories' => [
                    [
                        'name' => 'Camisetas',
                        'subcategories' => ['Manga corta', 'Manga larga', 'Polo', 'Sin mangas'],
                    ],
                    [
                        'name' => 'Pantalones',
                        'subcategories' => ['Jeans', 'Cargo', 'Deportivos', 'Bermudas'],
                    ],
                    [
                        'name' => 'Chaquetas',
                        'subcategories' => ['Deportivas', 'Casuales', 'Impermeables'],
                    ],
                ],
            ],
            [
                'name' => 'Calzado',
                'description' => 'Zapatos, tenis y sandalias para toda ocasión',
                'categories' => [
                    [
                        'name' => 'Tenis',
                        'subcategories' => ['Running', 'Casual', 'Skateboard'],
                    ],
                    [
                        'name' => 'Zapatos',
                        'subcategories' => ['Formales', 'Mocasines', 'Botas'],
                    ],
                    [
                        'name' => 'Sandalias',
                        'subcategories' => ['Playa', 'Urbanas'],
                    ],
                ],
            ],
            [
                'name' => 'Accesorios',
                'description' => 'Complementos y accesorios de moda',
                'categories' => [
                    [
                        'name' => 'Gorras',
                        'subcategories' => ['Snapback', 'Visera', 'Beanie'],
                    ],
                    [
                        'name' => 'Bolsos',
                        'subcategories' => ['Maletines', 'Morral', 'Riñonera'],
                    ],
                    [
                        'name' => 'Joyería',
                        'subcategories' => ['Collares', 'Pulseras', 'Aretes'],
                    ],
                ],
            ],
            [
                'name' => 'Deportes',
                'description' => 'Ropa y equipo deportivo',
                'categories' => [
                    [
                        'name' => 'Fútbol',
                        'subcategories' => ['Camisetas', 'Guayos', 'Espinilleras'],
                    ],
                    [
                        'name' => 'Gimnasio',
                        'subcategories' => ['Licras', 'Tops', 'Guantes'],
                    ],
                ],
            ],
        ];

        // Productos de muestra por subcategoría
        $productTemplates = [
            // Ropa
            'Manga corta' => $this->ropaProductos('Camiseta Manga Corta'),
            'Manga larga' => $this->ropaProductos('Camiseta Manga Larga'),
            'Polo' => $this->ropaProductos('Polo Classic'),
            'Sin mangas' => $this->ropaProductos('Camiseta Sin Mangas'),
            'Jeans' => $this->ropaProductos('Jean Slim Fit', 89900, 120000),
            'Cargo' => $this->ropaProductos('Pantalón Cargo', 79900, 99900),
            'Deportivos' => $this->ropaProductos('Pantalón Deportivo', 59900, 79900),
            'Bermudas' => $this->ropaProductos('Bermuda Casual', 49900, 69900),
            'Deportivas' => $this->ropaProductos('Chaqueta Deportiva', 119900, 149900),
            'Casuales' => $this->ropaProductos('Chaqueta Casual', 139900, 179900),
            'Impermeables' => $this->ropaProductos('Chaqueta Impermeable', 159900, 199900),
            // Calzado
            'Running' => $this->ropaProductos('Tenis Running Pro', 219900, 279900),
            'Casual' => $this->ropaProductos('Tenis Casual Urban', 179900, 219900),
            'Skateboard' => $this->ropaProductos('Tenis Skate', 149900, 189900),
            'Formales' => $this->ropaProductos('Zapato Formal Oxford', 199900, 249900),
            'Mocasines' => $this->ropaProductos('Mocasín Clásico', 169900, 219900),
            'Botas' => $this->ropaProductos('Botas de Cuero', 239900, 299900),
            'Playa' => $this->ropaProductos('Sandalias de Playa', 39900, 59900),
            'Urbanas' => $this->ropaProductos('Sandalias Urbanas', 49900, 69900),
            // Accesorios
            'Snapback' => $this->ropaProductos('Gorra Snapback', 39900, 54900),
            'Visera' => $this->ropaProductos('Visera Deportiva', 29900, 39900),
            'Beanie' => $this->ropaProductos('Beanie Invierno', 34900, 44900),
            'Maletines' => $this->ropaProductos('Maletín Ejecutivo', 179900, 229900),
            'Morral' => $this->ropaProductos('Morral Urban', 129900, 169900),
            'Riñonera' => $this->ropaProductos('Riñonera Casual', 49900, 69900),
            'Collares' => $this->ropaProductos('Collar Minimalista', 29900, 39900),
            'Pulseras' => $this->ropaProductos('Pulsera Trenzada', 19900, 29900),
            'Aretes' => $this->ropaProductos('Aretes Geométricos', 24900, 34900),
            // Deportes
            'Camisetas' => $this->ropaProductos('Camiseta de Fútbol', 79900, 99900),
            'Guayos' => $this->ropaProductos('Guayos Pro FG', 189900, 239900),
            'Espinilleras' => $this->ropaProductos('Espinilleras Ligeras', 29900, 39900),
            'Licras' => $this->ropaProductos('Licra Compresión', 69900, 89900),
            'Tops' => $this->ropaProductos('Top Deportivo', 49900, 69900),
            'Guantes' => $this->ropaProductos('Guantes Gym', 34900, 44900),
        ];

        $order = 1;
        foreach ($catalog as $familyData) {
            $family = Family::create([
                'name' => $familyData['name'],
                'slug' => Str::slug($familyData['name']),
                'description' => $familyData['description'],
                'active' => true,
                'order' => $order++,
            ]);

            $catOrder = 1;
            foreach ($familyData['categories'] as $categoryData) {
                $category = Category::create([
                    'family_id' => $family->id,
                    'name' => $categoryData['name'],
                    'slug' => Str::slug($family->name.'-'.$categoryData['name']),
                    'active' => true,
                    'order' => $catOrder++,
                ]);

                $subOrder = 1;
                foreach ($categoryData['subcategories'] as $subName) {
                    $subcategory = Subcategory::create([
                        'category_id' => $category->id,
                        'name' => $subName,
                        'slug' => Str::slug($family->name.'-'.$category->name.'-'.$subName),
                        'active' => true,
                        'order' => $subOrder++,
                    ]);

                    // Crear productos para esta subcategoría
                    $templates = $productTemplates[$subName] ?? $this->ropaProductos($subName);
                    foreach ($templates as $productData) {
                        Product::create(array_merge($productData, [
                            'subcategory_id' => $subcategory->id,
                        ]));
                    }
                }
            }
        }

        $this->command->info('✅ Seeder completado: '.Family::count().' familias, '.Category::count().' categorías, '.Subcategory::count().' subcategorías, '.Product::count().' productos.');
    }

    // ──────────────────────────────────────────────
    // Genera 3 variantes de producto por subcategoría
    // ──────────────────────────────────────────────
    private function ropaProductos(string $baseName, float $price = 49900, float $comparePrice = 69900): array
    {
        $colors = ['Negro', 'Blanco', 'Azul', 'Rojo', 'Verde', 'Gris', 'Beige', 'Naranja'];
        $selected = collect($colors)->shuffle()->take(3);

        return $selected->map(function ($color, $index) use ($baseName, $price, $comparePrice) {
            $name = "{$baseName} {$color}";
            $slug = Str::slug($name.'-'.uniqid());
            $stock = fake()->numberBetween(0, 80);
            $isFeatured = $index === 0; // primer color es destacado
            $hasDiscount = fake()->boolean(60); // 60% tienen precio anterior

            return [
                'name' => $name,
                'slug' => $slug,
                'short_description' => "Cómodo y versátil {$baseName} en color {$color}. Ideal para el día a día.",
                'description' => "Descubre la comodidad y el estilo con el {$name}. Fabricado con materiales de alta calidad para ofrecerte durabilidad y confort. Disponible en talla S, M, L, XL.\n\nCaracterísticas:\n- Material premium\n- Costuras reforzadas\n- Lavable a máquina\n- Corte moderno",
                'price' => $price + fake()->numberBetween(-5000, 10000),
                'compare_price' => $hasDiscount ? $comparePrice : null,
                'stock' => $stock,
                'low_stock_threshold' => 5,
                'sku' => strtoupper(Str::random(3)).'-'.fake()->numberBetween(1000, 9999),
                'active' => true,
                'featured' => $isFeatured,
                'images' => [],
                'order' => $index + 1,
            ];
        })->values()->toArray();
    }
}
