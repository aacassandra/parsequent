# Parse Eloquent

This is a package for laravel framework especially for CRUD activities. This package uses the eloquent laravel style and doesn't leave the default eloquent style. It's just that this package makes it easier for us to perform CRUD activities without including the model dependency on the associated controller.

## Suported Features

- Create
- Read
- Update
- Delete
- Batch
- CountingObjects
- SignIn
- SignOut
- VerifyingEmails
- PasswordReset
- ValidatingSessionTokens
- CreateUser
  - With Role [Optional]
- UpdateUser
  - With Role [Optional]
- DeleteUser
- CreateRole
- ReadRole
- UpdateRole
- DeleteRole

## Installation in Laravel

This package can be used with Laravel 5 or higher.

1. This package publishes a config/parsequent.php file. If you already have a file by that name, you must rename or remove it.

2. You can install the package via composer:

```
composer require aacassandra/parsequent
```

4. In the \$providers array add the service providers in your config/app.php file:

```
'providers' => [
    /*
    * Package Service Providers
    */
    // ...
    Parsequent\ParseProvider::class,
];
```

5. Add the facade of this package to the \$aliases array.

```
'aliases' => [
    // ...
    'Parse' => Parsequent\Parse::class,
]
```

6. You must publish the config. which will later be on the config/parsequent.php config file with:

```
php artisan vendor:publish --provider="Parsequent\ParseProvider"
```

7. Now the Parse Class will be auto-loaded by Laravel.

## License

This project is licensed under the MIT License - see the [LICENSE.md](https://github.com/aacassandra/laraquent/blob/master/LICENSE) file for details
