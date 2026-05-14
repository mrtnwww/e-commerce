<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users    = User::where('is_admin', false)->get();
        $products = Product::active()->where('stock', '>', 5)->get();

        if ($users->isEmpty()) {
            $this->command->error('No hay usuarios clientes. Ejecuta primero el seeder de usuarios.');
            return;
        }

        if ($products->isEmpty()) {
            $this->command->error('No hay productos con stock suficiente.');
            return;
        }

        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $methods  = ['transfer', 'cash', 'nequi', 'daviplata'];
        $cities   = [
            ['city' => 'Medellín',    'dept' => 'Antioquia'],
            ['city' => 'Bogotá',      'dept' => 'Cundinamarca'],
            ['city' => 'Cali',        'dept' => 'Valle del Cauca'],
            ['city' => 'Barranquilla','dept' => 'Atlántico'],
            ['city' => 'Cartagena',   'dept' => 'Bolívar'],
            ['city' => 'Manizales',   'dept' => 'Caldas'],
            ['city' => 'Pereira',     'dept' => 'Risaralda'],
        ];

        $count = 30; // número de pedidos a crear

        for ($i = 0; $i < $count; $i++) {

            $user      = $users->random();
            $status    = fake()->randomElement($statuses);
            $location  = fake()->randomElement($cities);
            $numItems  = fake()->numberBetween(1, 4);
            $orderProds = $products->random(min($numItems, $products->count()));

            // Calcular montos
            $subtotal = 0;
            $lines    = [];

            foreach ($orderProds as $product) {
                $qty       = fake()->numberBetween(1, 3);
                $unitPrice = $product->price;
                $lineTotal = $unitPrice * $qty;
                $subtotal += $lineTotal;

                $lines[] = [
                    'product'    => $product,
                    'qty'        => $qty,
                    'unit_price' => $unitPrice,
                    'total'      => $lineTotal,
                ];
            }

            $hasDiscount   = fake()->boolean(30);
            $discountAmt   = $hasDiscount ? round($subtotal * fake()->randomFloat(2, 0.05, 0.20)) : 0;
            $shippingCost  = fake()->boolean(40) ? 0 : fake()->randomElement([8000, 10000, 12000, 15000]);
            $total         = $subtotal - $discountAmt + $shippingCost;

            // Fecha aleatoria en los últimos 6 meses
            $createdAt = fake()->dateTimeBetween('-6 months', 'now');

            $order = Order::create([
                'user_id'             => $user->id,
                'status'              => $status,
                'customer_name'       => $user->name,
                'customer_email'      => $user->email,
                'customer_phone'      => '3' . fake()->numerify('########'),
                'shipping_address'    => 'Calle ' . fake()->numberBetween(1, 200) . ' # ' . fake()->numberBetween(1, 99) . '-' . fake()->numberBetween(1, 99),
                'shipping_city'       => $location['city'],
                'shipping_department' => $location['dept'],
                'shipping_zip'        => fake()->numerify('######'),
                'subtotal'            => $subtotal,
                'shipping_cost'       => $shippingCost,
                'discount'            => $discountAmt,
                'total'               => $total,
                'payment_method'      => fake()->randomElement($methods),
                'payment_status'      => in_array($status, ['delivered', 'shipped']) ? 'paid' : 'pending',
                'notes'               => fake()->boolean(20) ? fake()->sentence() : null,
                'paid_at'             => in_array($status, ['delivered', 'shipped', 'processing']) ? $createdAt : null,
                'shipped_at'          => in_array($status, ['delivered', 'shipped']) ? $createdAt : null,
                'delivered_at'        => $status === 'delivered' ? $createdAt : null,
                'created_at'          => $createdAt,
                'updated_at'          => $createdAt,
            ]);

            // Crear items
            foreach ($lines as $line) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $line['product']->id,
                    'product_name' => $line['product']->name,
                    'product_sku'  => $line['product']->sku,
                    'quantity'     => $line['qty'],
                    'unit_price'   => $line['unit_price'],
                    'total'        => $line['total'],
                ]);
            }

            $this->command->line("  ✓ Pedido {$order->number} — {$status} — $" . number_format($total, 0, ',', '.'));
        }

        $this->command->info("\n✅ {$count} pedidos creados correctamente.");
    }
}
