<?php

Route::group([
  'namespace' => 'Marcohern\Slugifier\Http\Controllers',
  'prefix' => 'mh/slug/api',
  'middleware' => ['api']
], function () {
  Route::resource('slugifier','SlugifierController');
  
});