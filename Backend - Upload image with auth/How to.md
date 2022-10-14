### Installation

- `copy .env.example .env`
  - `.env >> edit name of DB_DATABASE`
  - `.env >> edit APP_DEBUG`
- `composer install`
- `php artisan key:generate`
- `php artisan make:database`
- `php artisan migrate`
- `rmdir "public/storage"`
- `php artisan storage:link`
- `php artisan serve`

### Reference

- https://medium.com/@mamyboyman/laravel-passport-%E0%B9%80%E0%B8%A3%E0%B8%B4%E0%B9%88%E0%B8%A1%E0%B8%95%E0%B9%89%E0%B8%99-7be169e63f23

- https://blog.logrocket.com/laravel-passport-a-tutorial-and-example-build/

- https://appdividend.com/2022/02/28/laravel-file-upload/

- https://www.positronx.io/laravel-rest-api-with-passport-authentication-tutorial/

- https://www.youtube.com/watch?v=OtbfDLB8wS4

### Create Project

- `composer create-project --prefer-dist laravel/laravel UploadImageApi 8.*`
  - `config database // .env >> setting schema name to upload_image_api`
  - `config database // config/database.php >> utf8 , utf8_unicode_ci`
- `composer require laravel/sanctum`

- `app\HTTP\Kernel.php // middlewareGroups > api >> EnsureFrontendRequestsAreStateful::class`
- `php artisan make:controller Auth/UserAuthController`
- `php artisan make:model Image -m`
- `php artisan make:controller Api/ImageController --api --model=Image`

- `Modes/User.php >> hasMany(Image::class)`

- `php artisan migrate`
- `php artisan storage:link`
- `route/api.php`
- `php artisan serve`
