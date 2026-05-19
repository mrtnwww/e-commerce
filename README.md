# Proyecto de grado Martin Wilches - Sistema ecommerce

Sistema de comercio electrónico desarrollado con **Laravel 11**, **Livewire 3**, **Alpine.js**, **Jetstream** y **MySQL**.

Incluye panel administrativo y tienda pública.

---

## Tabla de Contenidos

- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Autenticación](#autenticación)
- [Panel Administrativo](#panel-administrativo)
  - [Dashboard](#dashboard)
  - [Pedidos](#pedidos)
  - [Clientes](#clientes)
  - [Catálogo](#catálogo)
  - [Tienda](#tienda)
  - [Configuración](#configuración)
- [Tienda Pública](#tienda-pública)
  - [Catálogo y Filtros](#catálogo-y-filtros)
  - [Detalle de Producto](#detalle-de-producto)
  - [Carrito](#carrito)
  - [Checkout](#checkout)
  - [Confirmación de Pedido](#confirmación-de-pedido)
- [Área de Cliente](#área-de-cliente)
- [Modelos y Base de Datos](#modelos-y-base-de-datos)
- [Rutas](#rutas)
- [Roles y Permisos](#roles-y-permisos)
- [Componentes Reutilizables](#componentes-reutilizables)

---

## Requisitos

| Herramienta | Versión mínima |
|-------------|---------------|
| PHP | 8.2+ |
| Laravel | 12.x |
| MySQL | 8.0+ |
| Node.js | 18+ |
| Composer | 2.x |

Paquetes principales:
- `laravel/jetstream` con stack Livewire
- `livewire/livewire`
- `laravel/sanctum`

---

## Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/mrtnwww/e-commerce
cd e-commerce

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias JS
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

# 9. Iniciar servidor
php artisan serve
```

### Credenciales por defecto

| Campo | Valor |
|-------|-------|
| Email | admin@mitienda.com |
| Contraseña | password |
| Rol | Administrador |

> ⚠️ La contraseña del administrador debe ser cambiada de subir el proyecto a producción.

---

## Estructura del Proyecto

```
app/
├── Http/
│   ├── Middleware/
│   │   └── AdminMiddleware.php          # Protección rutas /admin
│   └── Responses/
│       ├── LoginResponse.php            # Redirección post-login validando rol de usuario
│       └── RegisterResponse.php         # Redirección post-registro
├── Livewire/
│   ├── Admin/
│   │   ├── Catalog/
│   │   │   ├── FamilyIndex.php
│   │   │   ├── CategoryIndex.php
│   │   │   └── SubcategoryIndex.php
│   │   ├── Orders/
│   │   │   └── OrderIndex.php
│   │   ├── Products/
│   │   │   └── ProductIndex.php
│   │   ├── BannerIndex.php
│   │   ├── CustomerIndex.php
│   │   ├── Dashboard.php
│   │   ├── DiscountIndex.php
│   │   └── Settings.php
│   └── Frontend/
│       ├── Account/
│       │   ├── AccountDashboard.php
│       │   ├── OrderDetail.php
│       │   ├── OrderHistory.php
│       │   └── Profile.php
│       ├── CartBadge.php
│       ├── CartComponent.php
│       ├── Checkout.php
│       ├── ProductDetail.php
│       └── Shop.php
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

## Autenticación

El sistema implementado utiliza **Laravel Jetstream + Fortify** para la autenticación. Las vistas están personalizadas con un diseño de dos paneles — panel decorativo a la izquierda con beneficios de la plataforma y formulario a la derecha.

### Flujo de Login

1. El usuario accede a la ruta `/login`
2. Ingresa email y contraseña
3. Fortify autentica las credenciales
4. `LoginResponse` evalúa el rol del usuario:
   - Si `is_admin = true` → redirige a `/admin`
   - Si `is_admin = false` → redirige a `/` (tienda)

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

1. El usuario accede a la ruta `/register`
2. Completa los campos nombre, email y contraseña
3. Se crea el usuario con `is_admin = false` por defecto
4. `RegisterResponse` redirige a la tienda `/`

### Cierre de Sesión

El logout está disponible en:
- **Panel admin**: sidebar inferior → "Cerrar sesión"
- **Tienda**: menú de navegación superior

Después del logout, el usuario es redirigido a la tienda `/`.

### Barra de Admin en la Tienda

Cuando un administrador navega por la tienda, aparece una barra en la parte superior con el botón **"← Volver al panel"** que redirige a `/admin`. Esta barra es invisible para usuarios que no tienen el rol de administrador.

---

## Panel Administrativo

Accesible desde la ruta `/admin`. Protegido por el middleware `admin` que verifica `is_admin = true`.

El layout incluye un sidebar colapsable con navegación por secciones.

### Dashboard

**Ruta:** `/admin`
**Componente:** `App\Livewire\Admin\Dashboard`

Muestra las métricas clave de la tienda en tiempo real:

| Métrica | Descripción |
|---------|-------------|
| Ventas del mes | Suma total de pedidos no cancelados del mes actual con delta vs mes anterior |
| Pedidos del mes | Cantidad de pedidos del mes actual con delta vs mes anterior |
| Clientes nuevos | Usuarios registrados en el mes actual |
| Stock bajo | Productos con stock ≤ umbral configurado |

También incluye:
- **Gráfico de barras** de ventas de los últimos 7 días
- **Widget de stock crítico** con los productos con stock bajo
- **Tabla de últimos 5 pedidos** con número de pedido, cliente, estado y total

### Pedidos

**Ruta:** `/admin/pedidos`
**Componente:** `App\Livewire\Admin\Orders\OrderIndex`

Gestión completa de pedidos con:

- **Búsqueda** por número de pedido, nombre o email del cliente
- **Filtro por estado** con conteo por cada estado
- **Ordenamiento** por fecha
- **Cambio de estado inline** desde un select en la tabla
- **Modal de detalle** con información de productos, información del cliente, montos y totales

**Estados disponibles:**

| Estado | Descripción |
|--------|-------------|
| `pending` | Pendiente — recién creado |
| `processing` | En proceso — confirmado |
| `shipped` | Enviado — en camino |
| `delivered` | Entregado — completado |
| `cancelled` | Cancelado |
| `refunded` | Reembolsado |

Al cambiar el estado de un pedido a `shipped` o `delivered` se registra automáticamente la fecha correspondiente (`shipped_at`, `delivered_at`).

### Clientes

**Ruta:** `/admin/clientes`
**Componente:** `App\Livewire\Admin\CustomerIndex`

Tabla de todos los usuarios con `is_admin = false`:

- **Búsqueda** por nombre o email
- **Ordenamiento** por nombre o fecha de registro
- **Columnas:** nombre, email, fecha de registro, total de pedidos, total gastado
- **Modal de detalle** con estadísticas del cliente y sus últimos 5 pedidos

### Catálogo

El catálogo está organizado en una jerarquía de tres niveles:

```
Familia
  └── Categoría
        └── Subcategoría
              └── Producto
```

#### Familias

**Ruta:** `/admin/familias`
**Componente:** `App\Livewire\Admin\Catalog\FamilyIndex`

CRUD completo con imagen, descripción y estado activo/inactivo. Muestra el conteo de categorías de cada familia.

#### Categorías

**Ruta:** `/admin/categorias`
**Componente:** `App\Livewire\Admin\Catalog\CategoryIndex`

CRUD con selección de familia padre, imagen y estado. Muestra el conteo de subcategorías. Filtrable por familia.

#### Subcategorías

**Ruta:** `/admin/subcategorias`
**Componente:** `App\Livewire\Admin\Catalog\SubcategoryIndex`

CRUD con selección de categoría padre y estado. Muestra el conteo de productos. Filtrable por categoría.

#### Productos

**Ruta:** `/admin/productos`
**Componente:** `App\Livewire\Admin\Products\ProductIndex`

CRUD completo con los siguientes campos:

| Campo | Descripción |
|-------|-------------|
| Nombre | Nombre del producto |
| SKU | Código único de inventario |
| Precio | Precio de venta |
| Precio anterior | Precio tachado para mostrar descuento |
| Stock | Unidades disponibles |
| Alerta stock bajo | Umbral para marcar stock crítico |
| Subcategoría | Categoría a la que pertenece |
| Descripción corta | Resumen para listados |
| Descripción completa | Descripción detallada del producto |
| Imágenes | Múltiples imágenes (almacenadas en `storage/products/`) |
| Activo | Visible en la tienda o no |
| Destacado | Aparece primero en listados |

Funcionalidades adicionales:
- **Filtro por subcategoría** y por nivel de stock
- **Toggle activo/inactivo** inline sin abrir el formulario
- **Indicador visual de stock** — verde normal, ámbar bajo, rojo agotado
- **Carga múltiple de imágenes** con preview

### Tienda

#### Descuentos y Cupones

**Ruta:** `/admin/descuentos`
**Componente:** `App\Livewire\Admin\DiscountIndex`

Gestión de cupones de descuento:

| Campo | Descripción |
|-------|-------------|
| Código | Código que ingresa el cliente (se guarda en mayúsculas) |
| Tipo | `percentage` (%) o `fixed` (valor fijo) |
| Valor | Porcentaje o monto del descuento |
| Pedido mínimo | Monto mínimo del carrito para aplicar |
| Máximo de usos | Límite de usos totales |
| Vencimiento | Fecha límite de validez |
| Activo | Activar/desactivar sin eliminar |

#### Banners

**Ruta:** `/admin/banners`
**Componente:** `App\Livewire\Admin\BannerIndex`

Gestión de banners para la tienda con vista previa de imagen, texto, subtítulo, botón con enlace y orden de aparición.

### Configuración

**Ruta:** `/admin/opciones`
**Componente:** `App\Livewire\Admin\Settings`

Configuración general de la tienda:

- **Información general:** nombre, email, teléfono, dirección, moneda, logo
- **Envíos:** costo de envío, monto mínimo para envío gratis, opción de envío gratis en todos los pedidos
- **Redes sociales:** Instagram, Facebook, WhatsApp

La configuración se persiste en `config/store.php` y es accesible en las vistas con `config('store.nombre_campo')`.

---

## Tienda Pública

### Catálogo y Filtros

**Ruta:** `/`
**Componente:** `App\Livewire\Frontend\Shop`

Vista principal de la tienda con:

**Sidebar de filtros (desktop):**
- Navegación por familias y categorías en árbol colapsable
- Rango de precio mínimo y máximo
- Toggle "Solo disponibles" para filtrar por stock

**Barra superior de categorías:**
- Acceso rápido a todas las familias desde el navbar

**Ordenamiento:**
| Opción | Descripción |
|--------|-------------|
| Destacados | Productos con `featured = true` primero |
| Más recientes | Por fecha de creación descendente |
| Precio: menor a mayor | Ordenado por precio ASC |
| Precio: mayor a menor | Ordenado por precio DESC |

**Grid de productos** — 2 columnas en móvil, 3 en tablet, 4 en desktop. Cada tarjeta muestra imagen, nombre, precio, precio anterior tachado, badge de descuento y botón "Agregar al carrito".

### Detalle de Producto

**Ruta:** `/producto/{slug}`
**Componente:** `App\Livewire\Frontend\ProductDetail`

- Galería de imágenes con miniaturas clicables
- Precio y precio anterior con porcentaje de descuento
- Indicador de stock (disponible, stock bajo, agotado)
- Selector de cantidad con límite según stock disponible
- Botón de agregar al carrito con feedback visual ("✓ Añadido al carrito")
- Descripción completa del producto
- Sección de productos relacionados (misma subcategoría)
- Breadcrumb de navegación

### Carrito

**Ruta:** `/carrito`
**Componente:** `App\Livewire\Frontend\CartComponent`

El carrito funciona tanto para usuarios autenticados (persistido en BD por `user_id`) como para invitados (persistido por `session_id`).

Funcionalidades:
- **Actualizar cantidad** con validación de stock máximo
- **Eliminar producto** individual
- **Aplicar cupón** con validación de vigencia, usos y monto mínimo
- **Resumen** con subtotal, descuento aplicado y total
- **Botón de checkout** (redirige al login si no está autenticado)

El badge del carrito en el navbar (`CartBadge`) se actualiza en tiempo real mediante el evento `cart-add` de Livewire.

### Checkout

**Ruta:** `/checkout` (requiere autenticación)
**Componente:** `App\Livewire\Frontend\Checkout`

Proceso de compra en 3 pasos con indicador de progreso:

**Paso 1 — Datos del cliente**
- Nombre completo
- Email
- Teléfono

**Paso 2 — Dirección de envío**
- Dirección
- Ciudad
- Departamento
- Código postal

**Paso 3 — Pago**
- Método de pago: transferencia bancaria, contra entrega, Nequi, Daviplata
- Cupón de descuento
- Notas del pedido
- Botón "Confirmar pedido"

Al confirmar el pedido:
1. Se crea el registro en `orders` con todos los datos
2. Se crean los registros en `order_items` para cada producto
3. Se descuenta el stock de cada producto
4. Se incrementa el contador de usos del cupón (si aplica)
5. Se vacía el carrito del usuario
6. Redirige a la página de confirmación

### Confirmación de Pedido

**Ruta:** `/pedido/{number}/confirmacion`
**Vista:** `frontend/order-success.blade.php`

Muestra resumen completo del pedido con número, productos, montos y dirección de envío. Botones para ver mis pedidos e ir a la tienda.

---

## Área de Cliente

Accesible en `/cuenta` (requiere autenticación).

### Dashboard de cuenta

**Ruta:** `/cuenta`
**Componente:** `App\Livewire\Frontend\Account\AccountDashboard`

Resumen de la cuenta con total de pedidos, total gastado y los 5 pedidos más recientes.

### Historial de Pedidos

**Ruta:** `/cuenta/pedidos`
**Componente:** `App\Livewire\Frontend\Account\OrderHistory`

Lista paginada de todos los pedidos del usuario con filtro por estado. Cada pedido muestra número, fecha, estado, productos y total.

### Detalle de Pedido

**Ruta:** `/cuenta/pedidos/{number}`
**Componente:** `App\Livewire\Frontend\Account\OrderDetail`

Vista completa del pedido con:
- **Timeline de estados** visual (Recibido → En proceso → Enviado → Entregado)
- Datos del cliente y dirección de envío
- Información de pago y fecha
- Tabla de productos con imagen, cantidad y precio
- Resumen de totales con descuentos

### Perfil

**Ruta:** `/cuenta/perfil`
**Componente:** `App\Livewire\Frontend\Account\Profile`

- Editar nombre, email y teléfono
- Cambiar contraseña con verificación de contraseña actual

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

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | PK |
| name | varchar | Nombre completo |
| email | varchar | Email único |
| password | varchar | Hash de contraseña |
| is_admin | boolean | `true` = administrador |
| phone | varchar | Teléfono (opcional) |

### Tabla: `families`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | PK |
| name | varchar | Nombre |
| slug | varchar | URL amigable único |
| description | text | Descripción |
| image | varchar | Ruta de imagen en storage |
| active | boolean | Visible en tienda |
| order | int | Orden de aparición |

### Tabla: `categories`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | PK |
| family_id | bigint | FK → families |
| name | varchar | Nombre |
| slug | varchar | URL amigable único |
| active | boolean | Visible en tienda |
| order | int | Orden de aparición |

### Tabla: `subcategories`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | PK |
| category_id | bigint | FK → categories |
| name | varchar | Nombre |
| slug | varchar | URL amigable único |
| active | boolean | Visible en tienda |
| order | int | Orden de aparición |

### Tabla: `products`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | PK |
| subcategory_id | bigint | FK → subcategories (nullable) |
| name | varchar | Nombre del producto |
| slug | varchar | URL amigable único |
| description | text | Descripción completa |
| short_description | text | Descripción corta |
| price | decimal(12,2) | Precio de venta |
| compare_price | decimal(12,2) | Precio anterior tachado |
| stock | int | Unidades disponibles |
| low_stock_threshold | int | Umbral de alerta (default 5) |
| sku | varchar | Código único de producto |
| images | json | Array de rutas de imágenes |
| active | boolean | Visible en tienda |
| featured | boolean | Producto destacado |
| order | int | Orden de aparición |

### Tabla: `orders`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | PK |
| user_id | bigint | FK → users (nullable) |
| number | varchar | Número único (ORD-00001) |
| status | enum | Estado del pedido |
| customer_name | varchar | Nombre del cliente |
| customer_email | varchar | Email del cliente |
| customer_phone | varchar | Teléfono (opcional) |
| shipping_address | text | Dirección completa |
| shipping_city | varchar | Ciudad |
| shipping_department | varchar | Departamento |
| subtotal | decimal(12,2) | Subtotal sin descuento |
| shipping_cost | decimal(12,2) | Costo de envío |
| discount | decimal(12,2) | Monto descontado |
| total | decimal(12,2) | Total final |
| payment_method | varchar | Método de pago |
| notes | text | Notas del cliente |
| paid_at | timestamp | Fecha de pago |
| shipped_at | timestamp | Fecha de envío |
| delivered_at | timestamp | Fecha de entrega |

### Tabla: `order_items`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | PK |
| order_id | bigint | FK → orders |
| product_id | bigint | FK → products (nullable) |
| product_name | varchar | Nombre snapshot |
| product_sku | varchar | SKU snapshot |
| quantity | int | Cantidad |
| unit_price | decimal(12,2) | Precio unitario snapshot |
| total | decimal(12,2) | Subtotal de la línea |

### Tabla: `carts`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | PK |
| user_id | bigint | FK → users (nullable) |
| session_id | varchar | ID de sesión para invitados |
| product_id | bigint | FK → products |
| quantity | int | Cantidad |

### Tabla: `discounts`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | PK |
| code | varchar | Código único en mayúsculas |
| type | enum | `percentage` o `fixed` |
| value | decimal(8,2) | Valor del descuento |
| minimum_order | decimal(12,2) | Monto mínimo requerido |
| max_uses | int | Límite de usos (null = ilimitado) |
| used_count | int | Usos realizados |
| active | boolean | Habilitado |
| expires_at | timestamp | Fecha de vencimiento |

---

## Rutas

### Rutas públicas (Frontend)

| Método | URI | Nombre | Descripción |
|--------|-----|---------|-------------|
| GET | `/` | `shop` | Catálogo principal |
| GET | `/producto/{slug}` | `product` | Detalle de producto |
| GET | `/carrito` | `cart` | Carrito de compras |
| GET | `/checkout` | `checkout` | Proceso de pago (auth) |
| GET | `/pedido/{number}/confirmacion` | `order.success` | Confirmación |

### Rutas de cuenta (requieren auth)

| Método | URI | Nombre | Descripción |
|--------|-----|---------|-------------|
| GET | `/cuenta` | `account.dashboard` | Dashboard de cuenta |
| GET | `/cuenta/pedidos` | `account.orders` | Historial de pedidos |
| GET | `/cuenta/pedidos/{number}` | `account.order.detail` | Detalle de pedido |
| GET | `/cuenta/perfil` | `account.profile` | Editar perfil |

### Rutas de administración (requieren auth + is_admin)

| Método | URI | Nombre | Descripción |
|--------|-----|---------|-------------|
| GET | `/admin` | `admin.dashboard` | Dashboard |
| GET | `/admin/pedidos` | `admin.orders` | Gestión de pedidos |
| GET | `/admin/clientes` | `admin.customers` | Gestión de clientes |
| GET | `/admin/productos` | `admin.products` | Gestión de productos |
| GET | `/admin/familias` | `admin.families` | Gestión de familias |
| GET | `/admin/categorias` | `admin.categories` | Gestión de categorías |
| GET | `/admin/subcategorias` | `admin.subcategories` | Gestión de subcategorías |
| GET | `/admin/descuentos` | `admin.discounts` | Gestión de cupones |
| GET | `/admin/banners` | `admin.banners` | Gestión de banners |
| GET | `/admin/opciones` | `admin.settings` | Configuración |

---

## Roles y Permisos

El sistema tiene dos roles definidos por el campo `is_admin` en la tabla `users`:

| Rol | is_admin | Acceso |
|-----|----------|--------|
| Administrador | `true` | Panel admin + tienda |
| Cliente | `false` | Tienda + área de cuenta |

### Middleware AdminMiddleware

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

### Crear un administrador manualmente

```bash
php artisan tinker
```

```php
\App\Models\User::where('email', 'usuario@ejemplo.com')->update(['is_admin' => true]);
```

---

## Componentes Reutilizables

### `<x-modal>`

Modal genérico reutilizable en todo el panel admin.

```blade
<x-modal title="Título del modal" maxWidth="max-w-lg" closeMethod="closeModal">
    {{-- Contenido --}}

    <x-slot name="footer">
        <button wire:click="closeModal">Cancelar</button>
        <button wire:click="save">Guardar</button>
    </x-slot>
</x-modal>
```

| Prop | Default | Descripción |
|------|---------|-------------|
| `title` | `''` | Título del header |
| `maxWidth` | `max-w-lg` | Ancho máximo del modal |
| `closeMethod` | `closeModal` | Método Livewire para cerrar |
| `$footer` | — | Slot opcional para botones |

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

Badge del carrito que muestra el número de ítems. Se actualiza automáticamente al agregar productos mediante el evento `cart-add`.

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

Crea toda la estructura del catálogo con productos de ejemplo:
- 4 familias
- 11 categorías
- 32 subcategorías
- ~96 productos con variantes de color

```bash
php artisan db:seed --class=ProductSeeder
```

Para recrear todo desde cero:

```bash
php artisan migrate:fresh --seed
```

---

## Variables de Entorno Importantes

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

*Documentación generada para el proyecto E-Commerce — Laravel 11 + Livewire 3 + Jetstream*
