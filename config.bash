# Migraciones
php artisan migrate

# Crear admin
php artisan db:seed --class=AdminUserSeeder

# Instalar Breeze para el auth
composer require laravel/breeze --dev
php artisan breeze:install livewire
php artisan migrate
npm run dev
