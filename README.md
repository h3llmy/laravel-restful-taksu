# Laravel Restful

## Install

```
composer require taksu-tech/laravel-restful
```

`Taksu\RestfulServiceProvider` is auto-discovered, no manual registration needed.

## CRUD Controller

Create a controller class and extends it from `CrudController`. 

Example: 

use `CommonModelTrait` on the model. 

```php
class Admin extends Model
{
    use ModelCommonTrait;
    ...
}
```


```php

namespace App\Http\Controllers;

use App\Models\Admin;
use Taksu\Restful\Controllers\CrudController;

class AdminController extends CrudController
{
    public function __construct()
    {
        parent::__construct(Admin::class);
    }
}
```

Add in the `routes\api.php` 

```
Route::apiResource('admins', AdminController::class);
```

Finally, query the API

```
GET localhost:8000/api/admins
```


To install the console commands, in `AppServiceProvider`, add the following: 

```
public function boot()
{
    if ($this->app->runningInConsole()) {
        $this->commands([
            Taksu\Console\Commands\MakeCrudController::class,
        ]);
    }
}
```

## Migrations

This package ships publishable migrations for its optional features (multi-tenancy, running numbers). Publish and run them with:

```
php artisan vendor:publish --tag=restful-migrations
php artisan migrate
```

## Multi-tenancy

Use `TenantAware` on a model to automatically scope queries to the current tenant, stamp `tenant_id` on create, and hide it from serialized output.

```php
use Taksu\Tenant\TenantAware;

class Admin extends Model
{
    use TenantAware;
    ...
}
```

Requirements:
- The model's table needs a `tenant_id` column (see [Migrations](#migrations)).
- `Taksu\Tenant\CurrentTenant` resolves the tenant from `Auth::user()?->tenant_id`, so your authenticated user model must expose a `tenant_id` attribute.

## Running numbers

Use `HasRunningNumber` on a model to auto-generate a padded, prefixed, per-tenant sequential number on create (e.g. invoice numbers).

```php
use Taksu\RunningNumber\HasRunningNumber;

class Invoice extends Model
{
    use HasRunningNumber;

    protected $runningNumberColumn = 'number'; // default: 'number'
    protected $runningNumberPrefix = 'INV-';   // default: ''
    protected $runningNumberPadding = 6;       // default: 6
}
```

This requires the `running_number_sequences` table (see [Migrations](#migrations)). Sequences are scoped per `tenant_id` when the model uses `TenantAware`, or globally otherwise.