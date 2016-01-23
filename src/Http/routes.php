<?php

Route::group(
    [
        'prefix' => 'translation',
        'namespace' => 'Anavel\Translation\Http\Controllers'
    ],
    function () {
        Route::get('/', [
            'as'   => 'anavel-translation.home',
            'uses' => 'HomeController@index'
        ]);

        Route::get('{param}/{param2?}', [
            'as'   => 'anavel-translation.file.edit',
            'uses' => 'FileController@edit'
        ]);

        Route::post('{param}/{param2?}', [
            'as'   => 'anavel-translation.file.create',
            'uses' => 'FileController@create'
        ]);

        Route::put('{param}/{param2?}', [
            'as'   => 'anavel-translation.file.update',
            'uses' => 'FileController@update'
        ]);
    }
);
