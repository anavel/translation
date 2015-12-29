# adobadomin-transleite

Manage laravel translation files from your admin panel. This package depends on [ADobadoMIN](https://github.com/anavallasuiza/adobadomin)

### Features

* Easily manage app and vendor translation files.
* Automatically reorders translations (alphabetically)
* Easily create new language lines
* Arrays supported

## Installation


## Configuration

Publish Transleite config file with `php artisan vendor:publish`

Include the files you want to manage within the `files` array, like this: 

```
    /*
    |--------------------------------------------------------------------------
    | Files to translate
    |--------------------------------------------------------------------------
    |
    */
    'files' => [
        'user'   => [
            'aFileName', 
            'anotherFileName'
        ],
        'vendor' => [
            'vendorname' => 'vendorFileName'
        ]
    ],
```

`user` is an array of filenames (without extension) located in Laravel's default folder (resources/lang/LOCALE_NAME). 
`vendor` is an associative array of filenames (without extension), keyed by vendorname, located in Laravel's default folder (resources/lang/vendor/VENDORNAME/LOCALE_NAME).

Transleite will read and then write those files, so your app must have write permissions to those folders. You must specify a disc driver for Laravel to use:

Within config/transleite.php:

```
    /*
    |--------------------------------------------------------------------------
    | File Disc Driver
    |--------------------------------------------------------------------------
    |
    | Disc driver pointing to resources/lang folder
    |
    */
    'filedriver'  => 'YOUR_DRIVER_NAME',
```

Within config/filesystem.php:

```
    'disks' => [
        [SOME OTHER FILE DRIVERS ],
        'YOUR_DRIVER_NAME' => [
            'driver' => 'local',
            'root'   => base_path('resources/lang'),
        ],
    ]

```
    
 
 ## How it works
 
 