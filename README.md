# Sistema Web de Ventas en Línea — E-Commerce

**Proyecto de grado · Martin Eduardo Wilches Pinto**
Universidad Nacional Abierta y a Distancia (UNAD) · CEAD Bucaramanga · 2026

Sistema de comercio electrónico desarrollado con **Laravel 12**, **Livewire 3**, **Alpine.js**, **Laravel Jetstream** y **MySQL 8**. Incluye panel administrativo completo y tienda pública con proceso de compra en línea.

---

## Tabla de Contenidos

- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Autenticación y Roles](#autenticación-y-roles)
- [Panel Administrativo](#panel-administrativo)
- [Tienda Pública](#tienda-pública)
- [Área de Cliente](#área-de-cliente)
- [Modelos y Base de Datos](#modelos-y-base-de-datos)
- [Rutas del Sistema](#rutas-del-sistema)
- [Componentes Reutilizables](#componentes-reutilizables)
- [Seeders](#seeders)
- [Variables de Entorno](#variables-de-entorno)

---

## Requisitos

| Herramienta | Versión mínima |
|-------------|----------------|
| PHP         | 8.2+           |
| Laravel     | 12.x           |
| MySQL       | 8.0+           |
| Node.js     | 18+            |
| Composer    | 2.x            |

**Paquetes principales:**
- `laravel/jetstream` con stack Livewire
- `livewire/livewire` 3.x
- `laravel/sanctum`

---

## Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/mrtnwww/e-commerce
cd e-commerce

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias JavaScript
npm install

# 4. Configurar entorno
cp .env.example .env
php artisan key:generate

# 5. Configurar base de datos en .env
DB_DATABASE=ecommerce
DB_USERNAME=root
DB_PASSWORD=

# 6. Ejecutar migraciones y seeders
php artisan migrate --seed

# 7. Crear enlace de almacenamiento
php artisan storage:link

# 8. Compilar assets
npm run dev

# 9. Iniciar servidor de desarrollo
php artisan serve
```

### Credenciales por defecto

| Campo      | Valor                   |
|------------|-------------------------|
| Email      | admin@mitienda.com      |
| Contraseña | password                |
| Rol        | Administrador           |

> ⚠️ **Importante:** La la contraseña del administrador se debe cambiar antes de subir el proyecto a producción.

---

## Estructura del Proyecto

```
app/
├── Http/
│   ├── Middleware/
│   │   └── AdminMiddleware.php           # Protección rutas /admin (is_admin = true)
│   └── Responses/
│       ├── LoginResponse.php             # Redirección post-login según rol
│       └── RegisterResponse.php          # Redirección post-registro a tienda
├── Livewire/
│   ├── Admin/
│   │   ├── Catalog/
│   │   │   ├── FamilyIndex.php           # CRUD familias
│   │   │   ├── CategoryIndex.php         # CRUD categorías
│   │   │   └── SubcategoryIndex.php      # CRUD subcategorías
│   │   ├── Orders/
│   │   │   └── OrderIndex.php            # Gestión de pedidos
│   │   ├── Products/
│   │   │   └── ProductIndex.php          # CRUD productos
│   │   ├── BannerIndex.php               # CRUD banners
│   │   ├── CustomerIndex.php             # Vista de clientes
│   │   ├── Dashboard.php                 # Métricas del negocio
│   │   ├── DiscountIndex.php             # CRUD cupones de descuento
│   │   └── Settings.php                  # Configuración general
│   └── Frontend/
│       ├── Account/
│       │   ├── AccountDashboard.php      # Resumen de cuenta del cliente
│       │   ├── OrderDetail.php           # Detalle de pedido con timeline
│       │   ├── OrderHistory.php          # Historial de pedidos
│       │   └── Profile.php              # Edición de perfil y contraseña
│       ├── CartBadge.php                 # Badge del carrito en navbar
│       ├── CartComponent.php             # Carrito de compras
│       ├── Checkout.php                  # Proceso de compra en 3 pasos
│       ├── ProductDetail.php             # Detalle de producto
│       └── Shop.php                      # Catálogo con filtros
├── Models/
│   ├── Banner.php
│   ├── Cart.php
│   ├── Category.php
│   ├── Discount.php
│   ├── Family.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── Product.php
│   ├── Subcategory.php
│   └── User.php
└── Providers/
    ├── AppServiceProvider.php
    └── FortifyServiceProvider.php

resources/views/
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
├── components/
│   ├── admin-icon.blade.php
│   ├── modal.blade.php
│   └── modal-confirm.blade.php
├── errors/
│   ├── 403.blade.php
│   └── 404.blade.php
├── frontend/
│   └── order-success.blade.php
├── layouts/
│   ├── admin.blade.php
│   ├── guest.blade.php
│   └── store.blade.php
└── livewire/
    ├── admin/
    │   ├── banners/
    │   ├── catalog/
    │   ├── customers/
    │   ├── dashboard/
    │   ├── discounts/
    │   ├── orders/
    │   ├── products/
    │   └── settings/
    └── frontend/
        ├── account/
        ├── cart/
        ├── checkout/
        ├── product/
        └── shop/
```

---

## Autenticación y Roles

El sistema utiliza **Laravel Jetstream + Fortify** para la autenticación. Las vistas están personalizadas con un diseño de dos paneles: panel decorativo izquierdo con beneficios de la plataforma y formulario a la derecha.

### Roles del sistema

| Rol           | Campo `is_admin` | Acceso                          |
|---------------|------------------|---------------------------------|
| Administrador | `true`           | Panel `/admin` + tienda pública |
| Cliente       | `false`          | Tienda + área de cuenta `/cuenta` |

### Flujo de Login

1. El usuario accede a `/login` e ingresa email y contraseña.
2. Fortify autentica las credenciales contra la tabla `users`.
3. `LoginResponse` evalúa el rol:
   - `is_admin = true` → redirige a `/admin`
   - `is_admin = false` → redirige a `/` (tienda)

```php
// app/Http/Responses/LoginResponse.php
public function toResponse($request)
{
    $redirectTo = Auth::user()->is_admin
        ? route('admin.dashboard')
        : route('shop');

    return redirect()->intended($redirectTo);
}
```

### Flujo de Registro

1. El usuario accede a `/register` y completa nombre, email y contraseña.
2. Se crea el usuario con `is_admin = false` por defecto.
3. `RegisterResponse` redirige a la tienda `/`.

### Middleware de Autorización

```php
// app/Http/Middleware/AdminMiddleware.php
public function handle(Request $request, Closure $next): Response
{
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    if (!auth()->user()->is_admin) {
        abort(403, 'Acceso no autorizado.');
    }

    return $next($request);
}
```

Registrado en `bootstrap/app.php` con el alias `admin`.

### Barra de administrador en la tienda

Cuando un administrador navega por la tienda, aparece una barra superior con el botón **"← Volver al panel"** que redirige a `/admin`. Esta barra es invisible para clientes.

### Crear un administrador manualmente

```bash
php artisan tinker
```

```php
\App\Models\User::where('email', 'usuario@ejemplo.com')->update(['is_admin' => true]);
```

---

## Panel Administrativo

Accesible desde `/admin`. Protegido por el middleware `admin`.

### Dashboard

**Ruta:** `/admin` · **Componente:** `App\Livewire\Admin\Dashboard`

Métricas en tiempo real:

| Métrica          | Descripción                                                      |
|------------------|------------------------------------------------------------------|
| Ventas del mes   | Suma de pedidos no cancelados del mes, con delta vs. mes anterior |
| Pedidos del mes  | Cantidad de pedidos del mes, con delta vs. mes anterior          |
| Clientes nuevos  | Usuarios registrados en el mes actual                            |
| Stock bajo       | Productos con stock ≤ umbral configurado                         |

Incluye también: gráfico de barras de ventas últimos 7 días, widget de stock crítico y tabla de últimos 5 pedidos.

### Pedidos

**Ruta:** `/admin/pedidos` · **Componente:** `App\Livewire\Admin\Orders\OrderIndex`

- Búsqueda por número de pedido, nombre o email del cliente.
- Filtro por estado con conteo por cada estado.
- Cambio de estado inline desde select en la tabla.
- Modal de detalle con productos, cliente, montos y totales.

**Estados de pedido:**

| Estado       | Descripción              |
|--------------|--------------------------|
| `pending`    | Pedido recién creado     |
| `processing` | Pedido confirmado        |
| `shipped`    | Pedido en camino         |
| `delivered`  | Pedido completado        |
| `cancelled`  | Pedido cancelado         |
| `refunded`   | Pedido reembolsado       |

Al cambiar a `shipped` o `delivered` se registra automáticamente `shipped_at` o `delivered_at`.

### Clientes

**Ruta:** `/admin/clientes` · **Componente:** `App\Livewire\Admin\CustomerIndex`

Tabla de usuarios con `is_admin = false`. Búsqueda por nombre o email. Modal de detalle con estadísticas y últimos 5 pedidos del cliente.

### Catálogo

El catálogo está organizado en una **jerarquía de cuatro niveles**:

```
Familia
  └── Categoría
        └── Subcategoría
              └── Producto
```

| Nivel         | Ruta                    | Componente         |
|---------------|-------------------------|--------------------|
| Familias      | `/admin/familias`       | `FamilyIndex`      |
| Categorías    | `/admin/categorias`     | `CategoryIndex`    |
| Subcategorías | `/admin/subcategorias`  | `SubcategoryIndex` |
| Productos     | `/admin/productos`      | `ProductIndex`     |

#### Productos — campos del formulario

| Campo                | Descripción                                    |
|----------------------|------------------------------------------------|
| Nombre               | Nombre del producto                            |
| SKU                  | Código único de inventario                     |
| Precio               | Precio de venta                                |
| Precio anterior      | Precio tachado para mostrar descuento          |
| Stock                | Unidades disponibles                           |
| Alerta stock bajo    | Umbral para marcar stock crítico (default: 5)  |
| Subcategoría         | Nivel de catálogo al que pertenece             |
| Descripción corta    | Resumen para listados                          |
| Descripción completa | Descripción detallada                          |
| Imágenes             | Múltiples imágenes en `storage/products/`      |
| Activo               | Visible en tienda o no                         |
| Destacado            | Aparece primero en listados                    |

### Descuentos y Cupones

**Ruta:** `/admin/descuentos` · **Componente:** `App\Livewire\Admin\DiscountIndex`

| Campo          | Descripción                                        |
|----------------|----------------------------------------------------|
| Código         | Código del cupón (se almacena en mayúsculas)       |
| Tipo           | `percentage` (%) o `fixed` (valor fijo)            |
| Valor          | Porcentaje o monto del descuento                   |
| Pedido mínimo  | Monto mínimo del carrito para aplicar              |
| Máximo de usos | Límite total de usos (null = ilimitado)            |
| Vencimiento    | Fecha límite de validez                            |
| Activo         | Activar/desactivar sin eliminar el cupón           |

### Banners

**Ruta:** `/admin/banners` · **Componente:** `App\Livewire\Admin\BannerIndex`

Gestión de banners con vista previa, texto, subtítulo, botón con enlace y orden de aparición.

### Configuración General

**Ruta:** `/admin/opciones` · **Componente:** `App\Livewire\Admin\Settings`

- **Información general:** nombre, email, teléfono, dirección, moneda, logo.
- **Envíos:** costo de envío, monto para envío gratis.
- **Redes sociales:** Instagram, Facebook, WhatsApp.

Configuración persistida en `config/store.php` y accesible con `config('store.campo')`.

---

## Tienda Pública

### Catálogo y Filtros

**Ruta:** `/` · **Componente:** `App\Livewire\Frontend\Shop`

- Sidebar de filtros: árbol de familias/categorías, rango de precios, toggle "Solo disponibles".
- Ordenamiento: destacados, más recientes, precio menor a mayor, precio mayor a menor.
- Grid responsivo: 2 columnas móvil, 3 tablet, 4 desktop.

### Detalle de Producto

**Ruta:** `/producto/{slug}` · **Componente:** `App\Livewire\Frontend\ProductDetail`

- Galería de imágenes con miniaturas.
- Indicador de stock (disponible / stock bajo / agotado).
- Selector de cantidad con límite según stock.
- Productos relacionados de la misma subcategoría.

### Carrito de Compras

**Ruta:** `/carrito` · **Componente:** `App\Livewire\Frontend\CartComponent`

Persiste para usuarios autenticados (`user_id`) e invitados (`session_id`). El badge del navbar se actualiza en tiempo real mediante el evento `cart-add` de Livewire.

- Actualizar cantidad con validación de stock máximo.
- Eliminar producto individual.
- Aplicar cupón con validación completa.
- Resumen con subtotal, descuento y total.

### Checkout

**Ruta:** `/checkout` (requiere autenticación) · **Componente:** `App\Livewire\Frontend\Checkout`

Proceso en **3 pasos** con indicador de progreso:

| Paso | Datos requeridos                                  |
|------|---------------------------------------------------|
| 1    | Nombre completo, email, teléfono                  |
| 2    | Dirección, ciudad, departamento, código postal    |
| 3    | Método de pago, cupón, notas, confirmar pedido    |

**Métodos de pago disponibles:** transferencia bancaria, contra entrega, Nequi, Daviplata.

**Al confirmar el pedido el sistema:**
1. Crea el registro en `orders`.
2. Crea los registros en `order_items`.
3. Descuenta el stock de cada producto.
4. Incrementa el contador de usos del cupón si aplica.
5. Vacía el carrito del usuario.
6. Redirige a `/pedido/{number}/confirmacion`.

### Confirmación de Pedido

**Ruta:** `/pedido/{number}/confirmacion` · **Vista:** `frontend/order-success.blade.php`

Resumen completo del pedido con número, productos, montos y dirección de envío.

---

## Área de Cliente

Accesible en `/cuenta` (requiere autenticación).

| Ruta                        | Componente        | Descripción                                         |
|-----------------------------|-------------------|-----------------------------------------------------|
| `/cuenta`                   | AccountDashboard  | Resumen: total pedidos, total gastado, últimos 5    |
| `/cuenta/pedidos`           | OrderHistory      | Lista paginada de pedidos con filtro por estado     |
| `/cuenta/pedidos/{number}`  | OrderDetail       | Timeline de estados, productos, totales             |
| `/cuenta/perfil`            | Profile           | Editar nombre, email, teléfono y contraseña         |

---

## Modelos y Base de Datos

### Diagrama de relaciones

```
users
  └──< orders
         └──< order_items >──── products
carts >──── products
families
  └──< categories
         └──< subcategories
                └──< products
discounts
banners
```

### Tabla: `users`

| Campo    | Tipo    | Descripción                     |
|----------|---------|---------------------------------|
| id       | bigint  | PK                              |
| name     | varchar | Nombre completo                 |
| email    | varchar | Email único                     |
| password | varchar | Hash de contraseña              |
| is_admin | boolean | `true` = administrador          |
| phone    | varchar | Teléfono (opcional)             |

### Tabla: `families`

| Campo       | Tipo    | Descripción                  |
|-------------|---------|------------------------------|
| id          | bigint  | PK                           |
| name        | varchar | Nombre                       |
| slug        | varchar | URL amigable único           |
| description | text    | Descripción                  |
| image       | varchar | Ruta de imagen en storage    |
| active      | boolean | Visible en tienda            |
| order       | int     | Orden de aparición           |

### Tabla: `categories`

| Campo     | Tipo    | Descripción          |
|-----------|---------|----------------------|
| id        | bigint  | PK                   |
| family_id | bigint  | FK → families        |
| name      | varchar | Nombre               |
| slug      | varchar | URL amigable único   |
| active    | boolean | Visible en tienda    |
| order     | int     | Orden de aparición   |

### Tabla: `subcategories`

| Campo       | Tipo    | Descripción          |
|-------------|---------|----------------------|
| id          | bigint  | PK                   |
| category_id | bigint  | FK → categories      |
| name        | varchar | Nombre               |
| slug        | varchar | URL amigable único   |
| active      | boolean | Visible en tienda    |
| order       | int     | Orden de aparición   |

### Tabla: `products`

| Campo               | Tipo          | Descripción                          |
|---------------------|---------------|--------------------------------------|
| id                  | bigint        | PK                                   |
| subcategory_id      | bigint        | FK → subcategories (nullable)        |
| name                | varchar       | Nombre del producto                  |
| slug                | varchar       | URL amigable único                   |
| description         | text          | Descripción completa                 |
| short_description   | text          | Descripción corta para listados      |
| price               | decimal(12,2) | Precio de venta                      |
| compare_price       | decimal(12,2) | Precio anterior tachado              |
| stock               | int           | Unidades disponibles                 |
| low_stock_threshold | int           | Umbral de alerta (default: 5)        |
| sku                 | varchar       | Código único de producto             |
| images              | json          | Array de rutas de imágenes           |
| active              | boolean       | Visible en tienda                    |
| featured            | boolean       | Producto destacado                   |
| order               | int           | Orden de aparición                   |

### Tabla: `orders`

| Campo               | Tipo          | Descripción                         |
|---------------------|---------------|-------------------------------------|
| id                  | bigint        | PK                                  |
| user_id             | bigint        | FK → users (nullable)               |
| number              | varchar       | Número único de pedido (ORD-00001)  |
| status              | enum          | Estado del pedido                   |
| customer_name       | varchar       | Nombre del cliente (snapshot)       |
| customer_email      | varchar       | Email del cliente (snapshot)        |
| customer_phone      | varchar       | Teléfono (opcional)                 |
| shipping_address    | text          | Dirección completa                  |
| shipping_city       | varchar       | Ciudad                              |
| shipping_department | varchar       | Departamento                        |
| subtotal            | decimal(12,2) | Subtotal sin descuento              |
| shipping_cost       | decimal(12,2) | Costo de envío                      |
| discount            | decimal(12,2) | Monto descontado por cupón          |
| total               | decimal(12,2) | Total final del pedido              |
| payment_method      | varchar       | Método de pago seleccionado         |
| notes               | text          | Notas del cliente                   |
| paid_at             | timestamp     | Fecha de pago                       |
| shipped_at          | timestamp     | Fecha de envío                      |
| delivered_at        | timestamp     | Fecha de entrega                    |

### Tabla: `order_items`

| Campo        | Tipo          | Descripción                          |
|--------------|---------------|--------------------------------------|
| id           | bigint        | PK                                   |
| order_id     | bigint        | FK → orders                          |
| product_id   | bigint        | FK → products (nullable)             |
| product_name | varchar       | Nombre snapshot (histórico)          |
| product_sku  | varchar       | SKU snapshot (histórico)             |
| quantity     | int           | Cantidad                             |
| unit_price   | decimal(12,2) | Precio unitario snapshot             |
| total        | decimal(12,2) | Subtotal de la línea                 |

### Tabla: `carts`

| Campo      | Tipo    | Descripción                        |
|------------|---------|------------------------------------|
| id         | bigint  | PK                                 |
| user_id    | bigint  | FK → users (nullable)              |
| session_id | varchar | ID de sesión para invitados        |
| product_id | bigint  | FK → products                      |
| quantity   | int     | Cantidad                           |

### Tabla: `discounts`

| Campo         | Tipo          | Descripción                              |
|---------------|---------------|------------------------------------------|
| id            | bigint        | PK                                       |
| code          | varchar       | Código único en mayúsculas               |
| type          | enum          | `percentage` o `fixed`                   |
| value         | decimal(8,2)  | Valor del descuento                      |
| minimum_order | decimal(12,2) | Monto mínimo del carrito requerido       |
| max_uses      | int           | Límite de usos (null = ilimitado)        |
| used_count    | int           | Usos realizados                          |
| active        | boolean       | Cupón habilitado                         |
| expires_at    | timestamp     | Fecha de vencimiento                     |

### Tabla: `banners`

| Campo     | Tipo    | Descripción                       |
|-----------|---------|-----------------------------------|
| id        | bigint  | PK                                |
| image     | varchar | Ruta de imagen en storage         |
| title     | varchar | Texto principal del banner        |
| subtitle  | varchar | Subtítulo                         |
| button    | varchar | Texto del botón                   |
| url       | varchar | Enlace del botón                  |
| order     | int     | Orden de aparición                |
| active    | boolean | Visible en tienda                 |

---

## Rutas del Sistema

### Rutas públicas — Tienda

| Método | URI                              | Nombre          | Descripción                          |
|--------|----------------------------------|-----------------|--------------------------------------|
| GET    | `/`                              | `shop`          | Catálogo principal                   |
| GET    | `/producto/{slug}`               | `product`       | Detalle de producto                  |
| GET    | `/carrito`                       | `cart`          | Carrito de compras                   |
| GET    | `/checkout`                      | `checkout`      | Proceso de pago (requiere auth)      |
| GET    | `/pedido/{number}/confirmacion`  | `order.success` | Confirmación de pedido               |

### Rutas de cuenta — Cliente (requieren autenticación)

| Método | URI                          | Nombre                 | Descripción               |
|--------|------------------------------|------------------------|---------------------------|
| GET    | `/cuenta`                    | `account.dashboard`    | Dashboard de cuenta       |
| GET    | `/cuenta/pedidos`            | `account.orders`       | Historial de pedidos      |
| GET    | `/cuenta/pedidos/{number}`   | `account.order.detail` | Detalle de pedido         |
| GET    | `/cuenta/perfil`             | `account.profile`      | Editar perfil             |

### Rutas de administración (requieren autenticación + `is_admin = true`)

| Método | URI                      | Nombre                  | Descripción              |
|--------|--------------------------|-------------------------|--------------------------|
| GET    | `/admin`                 | `admin.dashboard`       | Dashboard                |
| GET    | `/admin/pedidos`         | `admin.orders`          | Gestión de pedidos       |
| GET    | `/admin/clientes`        | `admin.customers`       | Gestión de clientes      |
| GET    | `/admin/productos`       | `admin.products`        | Gestión de productos     |
| GET    | `/admin/familias`        | `admin.families`        | Gestión de familias      |
| GET    | `/admin/categorias`      | `admin.categories`      | Gestión de categorías    |
| GET    | `/admin/subcategorias`   | `admin.subcategories`   | Gestión de subcategorías |
| GET    | `/admin/descuentos`      | `admin.discounts`       | Gestión de cupones       |
| GET    | `/admin/banners`         | `admin.banners`         | Gestión de banners       |
| GET    | `/admin/opciones`        | `admin.settings`        | Configuración general    |

---

## Componentes Reutilizables

### `<x-modal>`

Modal genérico reutilizable en el panel admin.

```blade
<x-modal title="Título" maxWidth="max-w-lg" closeMethod="closeModal">
    {{-- Contenido --}}
    <x-slot name="footer">
        <button wire:click="closeModal">Cancelar</button>
        <button wire:click="save">Guardar</button>
    </x-slot>
</x-modal>
```

| Prop          | Default      | Descripción                         |
|---------------|--------------|-------------------------------------|
| `title`       | `''`         | Título del header                   |
| `maxWidth`    | `max-w-lg`   | Ancho máximo del modal              |
| `closeMethod` | `closeModal` | Método Livewire para cerrar         |
| `$footer`     | —            | Slot opcional para botones          |

### `<x-modal-confirm>`

Modal de confirmación para operaciones de borrado.

```blade
<x-modal-confirm
    title="¿Eliminar producto?"
    description="Esta acción no se puede deshacer."
    confirmMethod="delete"
/>
```

### `<x-admin-icon>`

Iconos SVG del sidebar admin.

```blade
<x-admin-icon name="grid" class="w-4 h-4" />
```

Íconos disponibles: `grid`, `shopping-bag`, `users`, `cube`, `collection`, `tag`, `archive`, `ticket`, `photo`, `cog`, `chart-bar`.

### `<livewire:frontend.cart-badge>`

Badge del carrito con conteo en tiempo real. Se actualiza con el evento `cart-add`.

```blade
<livewire:frontend.cart-badge />
```

---

## Seeders

### AdminUserSeeder

Crea el usuario administrador por defecto.

```bash
php artisan db:seed --class=AdminUserSeeder
```

### ProductSeeder

Crea toda la estructura del catálogo con datos de ejemplo:
- 4 familias
- 11 categorías
- 32 subcategorías
- ~96 productos con variantes de color

```bash
php artisan db:seed --class=ProductSeeder
```

Para recrear todo el esquema desde cero:

```bash
php artisan migrate:fresh --seed
```

---

## Variables de Entorno

```env
APP_NAME="Mi Tienda"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public
```

---
