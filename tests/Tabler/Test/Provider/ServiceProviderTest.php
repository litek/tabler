<?php
namespace Tabler\Test\Provider;
use Silex\Application;
use Tabler\Provider\TablerServiceProvider;
use Tabler\Connection;

class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{
  public function testRegister()
  {
    $app = new Application;
    $app->register(new TablerServiceProvider);
    $this->assertInstanceOf('Tabler\\Connection', $app['db']);
  }
}
