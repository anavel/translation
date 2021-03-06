# Anavel translation [![Build Status](https://travis-ci.org/anavel/translation.svg?branch=master)](https://travis-ci.org/anavel/translation) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/anavel/translation/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/anavel/translation/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/anavel/translation/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/anavel/translation/?branch=master) [![StyleCI](https://styleci.io/repos/47195974/shield)](https://styleci.io/repos/47195974)

Manage laravel translation files from your admin panel. This package depends on [Anavel foundation](https://github.com/anavel/foundation)

### Features

* Easily manage app and vendor translation files.
* Automatically reorders translations (alphabetically)
* Easily create new language lines
* Arrays supported

## Installation


## Configuration

Publish translation config file with `php artisan vendor:publish`

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

This package will read and then write those files, so your app must have write permissions to those folders. You must specify a disc driver for Laravel to use:

config/anavel-translation.php:

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

config/filesystem.php:

```
    'disks' => [
        [SOME OTHER FILE DRIVERS ],
        'YOUR_DRIVER_NAME' => [
            'driver' => 'local',
            'root'   => base_path('resources/lang'),
        ],
    ]
```

## Versioning

If you use a versioning system (such as git) you should add the language folder to your gitignore. Otherwise, you might
get conflicts if different users update the translations.

## How it works

 Translations reads the files that you specify in the config and displays their content in a form, tabbed by locale.
 The translation key becomes the input label and the translation itself becomes the input value.
 Locales are taken from the Anavel foundation config.

 To make the translation process easier, Translation shows the same language entries in all locales, even if a key is missing in a given locale.
 In that case, the displayed text will be taken from the fallback locale (as Laravel does).

 When saved, translations are written back to the files. If a file doesn't exist in a locale, a new one is created.
