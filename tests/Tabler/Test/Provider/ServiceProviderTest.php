<?php
namespace Tabler\Test\Provider;
use Silex\Application;
use Tabler\Provider\DbServiceProvider;
use Tabler\Connection;

class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{
  public function testRegister()
  {
    $app = new Application;
    $app->register(new DbServiceProvider);
    $this->assertInstanceOf('Tabler\\Connection', $app['db']);
  }
}
