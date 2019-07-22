<?php

Route::group([
  'namespace' => 'Marcohern\Slugifier\Http\Controllers',
  'prefix' => 'slugifier',
  'middleware' => ['api']
], function () {
  Route::post('/slugify', 'SlugifierController@slugify');
  Route::get ('/check'  , 'SlugifierController@check');
  Route::post('/storex' , 'SlugifierController@storex');
  Route::post('/'       , 'SlugifierController@store');
  Route::get ('/'       , 'SlugifierController@index');
  
});