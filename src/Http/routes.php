<?php

Route::group(
    [
        'prefix' => 'transleite',
        'namespace' => 'ANavallaSuiza\Transleite\Http\Controllers'
    ],
    function () {
        Route::get('/', [
            'as'   => 'transleite.home',
            'uses' => 'HomeController@index'
        ]);

        Route::get('{param}/{param2?}', [
            'as'   => 'transleite.file.edit',
            'uses' => 'FileController@edit'
        ]);

        Route::post('{param}/{param2?}', [
            'as'   => 'transleite.file.create',
            'uses' => 'FileController@create'
        ]);

        Route::put('{param}/{param2?}', [
            'as'   => 'transleite.file.update',
            'uses' => 'FileController@update'
        ]);
    }
);
