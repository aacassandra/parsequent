<p align="center">
  <img src="https://user-images.githubusercontent.com/29236058/101732025-2064be00-3aef-11eb-9f23-798f3757bc9b.png">
</p>

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

## Object Format

| No  | Method | Parameters                                        | Options                                                                                                                                                                                                     |
| --- | ------ | ------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 1   | Create | className [string], data [array], options [array] | masterKey [bool]                                                                                                                                                                                            |
| 2   | Read   | className ['string], options [array]              | objectId [string] <br/> where [array] <br/> orWhere [array] <br/> limit [int] <br/> skip [int] <br/> order [string] <br/> keys [array] <br/> include [array] <br/> relation [string] <br/> masterKey [bool] |
| 3   | Update | className [string], data [array], options [array] | objectId [string] <br/> where [array] <br/> orWhere [array] <br/> masterKey [bool]                                                                                                                          |
| 4   | Delete | className [string] , options [array]              | objectId [string] <br/> where [array] <br/> orWhere [array] <br/> masterKey [bool]                                                                                                                          |

### Example

#### 1. Create

To create a new object in Parse. You can follow an example as described below:

```
<?php
namespace App\Http\Controllers;
use Parsequent\Parse;
class DevController extends Controller
{
  public function dev(Request $request)
  {
    $create = Parse::Create('GameScore', [
      'score' => 1337,
      'playerName' => 'Sean Plott',
      'cheatMode' => false
    ]);
    if($create->status){
      // handling success
    }else{
      // handling error
    }
  }
}
```

The response body is a JSON object containing the objectId and the createdAt timestamp of the newly-created object:

```
{
  "output": {
    "createdAt": "2011-08-20T02:06:57.931Z",
    "objectId": "Ed1nuqPvcm"
  },
  "code": 201,
  "status": true
}
```

#### 2. Read

After you create an object, you can retrieve its content by calling a method. For example, to retrieve the object we created above:

```
<?php
namespace App\Http\Controllers;
use Parsequent\Parse;
class DevController extends Controller
{
  public function dev(Request $request)
  {
    $read = Parse::Read('GameScore', [
      'objectId' => 'Ed1nuqPvcm'
    ]);
    if($read->status){
      // handling success
    }else{
      // handling error
    }
  }
}
```

The response body is a JSON object containing all the user-provided fields, plus the createdAt, updatedAt, and objectId fields:

```
{
  "output": {
    "score": 1337,
    "playerName": "Sean Plott",
    "cheatMode": false,
    "skills": [
      "pwnage",
      "flying"
    ],
    "createdAt": "2011-08-20T02:06:57.931Z",
    "updatedAt": "2011-08-20T02:06:57.931Z",
    "objectId": "Ed1nuqPvcm"
  },
  "code": 200,
  "status": true
}
```

#### 3. Update

To change the data on an object that already exists, calling method Parse::Update. Any keys you don’t specify will remain unchanged, so you can update just a subset of the object’s data. For example, if we wanted to change the score field on a specific object:

```
<?php
namespace App\Http\Controllers;
use Parsequent\Parse;
class DevController extends Controller
{
  public function dev(Request $request)
  {
    $update = Parse::Update('GameScore', [
        'score' => 73453
    ], [
        'objectId' => 'Ed1nuqPvcm'
    ]);
    if($update->status){
      // handling success
    }else{
      // handling error
    }
  }
}
```

The response body is a JSON object containing just an updatedAt field with the timestamp of the update.

```
{
  "output": {
    "updatedAt": "2011-08-21T18:02:52.248Z"
  },
  "code": 200,
  "status": true
}
```

If you want to change multiple lines with conditionals, add a where / orwhere parameter in the options. here we will run 'Batch', to update multiple rows at once.

```
<?php
namespace App\Http\Controllers;
use Parsequent\Parse;
class DevController extends Controller
{
  public function dev(Request $request)
  {
    $update = Parse::Update('GameScore', [
        'score' => 73453
    ], [
        'where' => [
          ['cheatMode', 'equalTo', false]
        ]
    ]);
    if($update->status){
      // handling success
    }else{
      // handling error
    }
  }
}
```

Because to update multiple rows at once we use 'Batch', as explained on the official page. The response from batch will be a list with the same number of elements as the input list. Each item in the list with be a dictionary with either the success or error field set. The value of success will be the normal response to the equivalent REST command:

```
{
  "output": [
    [
      "succes": [
        "updatedAt": "2020-12-10T08:04:47.256Z"
      ]
    ],
    ...
  ],
  "code": 200,
  "status": true
}
```

#### 4. Delete

To delete an object from Parse Cloud, you can use the Parse::Delete method. Same as the update object method. You can delete a single object or multiple objects at once. To delete a single object, look at the example below:

```
<?php
namespace App\Http\Controllers;
use Parsequent\Parse;
class DevController extends Controller
{
  public function dev(Request $request)
  {
    $delete = Parse::Delete('GameScore', [
      'objectId' => 'Ed1nuqPvcm'
    ]);
    if($delete->status){
      // handling success
    }else{
      // handling error
    }
  }
}
```

The response body is a JSON object containing all the user-provided fields, plus the createdAt, updatedAt, and objectId fields:

```
{
  "output": {},
  "code": 200,
  "status": true
}
```

Whereas to delete multiple objects at once, you can do it like this:

```
<?php
namespace App\Http\Controllers;
use Parsequent\Parse;
class DevController extends Controller
{
  public function dev(Request $request)
  {
    $delete = Parse::Delete('GameScore', [
        'where' => [
          ['cheatMode', 'equalTo', true]
        ]
    ]);
    if($delete->status){
      // handling success
    }else{
      // handling error
    }
  }
}
```

And then the response will be like this

```
{
  "output": [
    [
      "succes": {}
    ],
    ...
  ],
  "code": 200,
  "status": true
}
```

## License

This project is licensed under the MIT License - see the [LICENSE.md](https://github.com/aacassandra/laraquent/blob/master/LICENSE) file for details
