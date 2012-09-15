<?php
namespace Tabler\Test;
use Tabler\Table;

class TableTest extends \PHPUnit_Framework_TestCase
{
  public function getMockConnection(array $methods)
  {
    $driver = new \Doctrine\DBAL\Driver\PDOSqlite\Driver;
    return $this->getMock('Tabler\\Connection', $methods, array(array(), $driver));
  }

  public function testInsert()
  {
    $conn  = $this->getMockConnection(array('insert'));
    $table = new Table($conn);

    $conn->expects($this->once())
         ->method('insert')
         ->with($this->isType('string'), array('name' => 'foo'));

    $table->insert(array('name' => 'foo'));
  }

  public function testUpdate()
  {
    $conn  = $this->getMockConnection(array('update'));
    $table = new Table($conn);

    $conn->expects($this->once())
         ->method('update')
         ->with($this->isType('string'), array('name' => 'foo'), array('id' => 1));

    $table->update(array('name' => 'foo'), array('id' => 1));
  }

  public function testDelete()
  {
    $conn  = $this->getMockConnection(array('delete'));
    $table = new Table($conn);

    $conn->expects($this->once())
         ->method('delete')
         ->with($this->isType('string'), array('id' => 1));

    $table->delete(array('id' => 1));
  }

  public function testFind()
  {
    $conn  = $this->getMockConnection(array('find'));
    $table = new Table($conn);

    $conn->expects($this->once())
         ->method('find')
         ->with($this->isType('string'), array('name' => 'foo'), array('id' => 1));

    $table->find(array('name' => 'foo'), array('id' => 1));
  }

  public function testFindAll()
  {
    $conn  = $this->getMockConnection(array('findAll'));
    $table = new Table($conn);

    $conn->expects($this->once())
         ->method('findAll')
         ->with($this->isType('string'), array('id', 'name'), array('id' => 1));

    $table->findAll(array('id', 'name'), array('id' => 1));
  }
}
