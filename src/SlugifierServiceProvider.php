<?php

namespace Marcohern\Slugifier;

use Illuminate\Support\ServiceProvider;

class SlugifierServiceProvider extends ServiceProvider {
  public function boot() {
    //$this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    $this->loadMigrationsFrom(__DIR__.'/../publishables/migrations');
  }
  
  public function register() {
    $this->registerPublishables();
  }

  private function registerPublishables() {
    $basePath = dirname(__DIR__);

    $publishables = [
      'config' => [
          //"$basePath/publishables/config/slugifier.php" => config_path('slugifier.php')
      ],
      'migrations' => [
        //"$basePath/publishables/migrations" => database_path('migrations')
      ],
    ];

    foreach($publishables as $group => $paths) {
        $this->publishes($paths, $group);
    }
  }
}