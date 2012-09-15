<?php
namespace Tabler\Provider;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;

class TablerServiceProvider extends DoctrineServiceProvider
{
  public function register(Application $app)
  {
    parent::register($app);
    
    $app['db.default_options'] += array(
      'wrapperClass' => 'Tabler\\Connection',
      'namespace' => ''
    );
  }
}
