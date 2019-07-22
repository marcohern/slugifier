<?php

Route::group([
  'namespace' => 'Marcohern\Slugifier\Http\Controllers',
  'prefix' => 'slugifier',
  'middleware' => ['api']
], function () {
  Route::post('/slugify'               , 'SlugifierController@slugify');
  Route::get ('/check/{entity}/{slug}' , 'SlugifierController@check');
  Route::get ('/check/{slug}'          , 'SlugifierController@check_global');
  Route::post('/storex/{entity}/{slug}', 'SlugifierController@storex');
  Route::post('/storex/{slug}'         , 'SlugifierController@storex_global');
  Route::post('/{entity}/{slug}'       , 'SlugifierController@store');
  Route::post('/{slug}'                , 'SlugifierController@store_global');
  Route::get ('/'                      , 'SlugifierController@index');
  
});