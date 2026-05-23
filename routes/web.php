<?php

use App\Livewire\Admin;
use App\Livewire\Frontend;
use App\Livewire\Frontend\OrderSuccess;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────
// TIENDA PÚBLICA (FRONTEND)
// ─────────────────────────────────────────────────────────────
Route::get('/', Frontend\Shop::class)->name('shop');
Route::get('/carrito', Frontend\CartComponent::class)->name('cart');
Route::get('/producto/{slug}', Frontend\ProductDetail::class)->name('product');

Route::get('/checkout', Frontend\Checkout::class)
    ->middleware('auth')
    ->name('checkout');

// Confirmación de pedido
Route::get('/pedido/{number}/confirmacion', OrderSuccess::class)->name('order.success');

// ─────────────────────────────────────────────────────────────
// ÁREA DE CLIENTE
// ─────────────────────────────────────────────────────────────
Route::middleware('auth')
    ->prefix('cuenta')
    ->name('account.')
    ->group(function () {
        Route::get('/perfil', Frontend\Account\Profile::class)->name('profile');
        Route::get('/', Frontend\Account\AccountDashboard::class)->name('dashboard');
        Route::get('/pedidos', Frontend\Account\OrderHistory::class)->name('orders');
        Route::get('/pedidos/{number}', Frontend\Account\OrderDetail::class)->name('order.detail');
    });

// ─────────────────────────────────────────────────────────────
// ADMIN — Panel administrativo
// ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', Admin\Dashboard::class)->name('dashboard');

        // Catalogo y productos
        Route::get('/subcategorias', Admin\Catalog\SubcategoryIndex::class)->name('subcategories');
        Route::get('/categorias', Admin\Catalog\CategoryIndex::class)->name('categories');
        Route::get('/productos', Admin\Products\ProductIndex::class)->name('products');
        Route::get('/familias', Admin\Catalog\FamilyIndex::class)->name('families');

        // Gestión de ventas y usuarios
        Route::get('/pedidos', Admin\Orders\OrderIndex::class)->name('orders');
        Route::get('/clientes', Admin\CustomerIndex::class)->name('customers');

        // Marketing y configuración
        Route::get('/descuentos', Admin\DiscountIndex::class)->name('discounts');
        Route::get('/banners', Admin\BannerIndex::class)->name('banners');
        Route::get('/opciones', Admin\Settings::class)->name('settings');
    });
