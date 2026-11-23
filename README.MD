# Lanchonete
## Requisitos
- PHP 8.2+, Composer
- MySQL
## Como rodar
1. Copie `.env.example` para `.env` e ajuste DB_*
2. `composer install` (se necessário)
3. `php artisan key:generate`
4. `php artisan migrate`
5. `php artisan serve`