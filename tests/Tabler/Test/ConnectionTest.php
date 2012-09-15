<?php
namespace Tabler\Test;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
  public function getConnection()
  {
    return \Doctrine\DBAL\DriverManager::getConnection(array(
      'pdo' => new \PDO('sqlite::memory:'),
      'wrapperClass' => 'Tabler\\Connection',
      'namespace' => 'App'
    ));
  }

  public function getMockConnection(array $methods)
  {
    $driver = new \Doctrine\DBAL\Driver\PDOSqlite\Driver;
    return $this->getMock('Tabler\\Connection', $methods, array(array(), $driver));
  }

  // @todo Flesh this out
  public function testArrayAccess()
  {
    $conn = $this->getMockConnection(array('getTableClass'));
    $table = $this->getMockForAbstractClass('Tabler\\Table', array($conn));

    $conn->expects($this->any())
         ->method('getTableClass')
         ->will($this->returnValue(get_class($table)));

    $this->assertEquals($table, $conn['table']);
    $this->assertEquals($conn, $conn['table']->getConnection());
  }

  public function testGetTableClass()
  {
    $conn = $this->getConnection();
    $names = array(
      'users' => 'Users',
      'user_accounts' => 'UserAccounts',
      'Users' => 'Users',
      'userAccounts' => 'UserAccounts',
      'user_has_friends' => 'UserHasFriends'
    );

    foreach ($names as $from => $to) {
      $this->assertEquals("App\\$to", $conn->getTableClass($from));
    }
  }

  public function testFind()
  {
    $conn = $this->getMockConnection(array('fetchObject'));
    $conn->expects($this->once())
         ->method('fetchObject')
         ->with($this->equalTo('SELECT * FROM users WHERE id = ? LIMIT 1'), array(1));
    $conn->find('users', array('id' => 1));

    $conn = $this->getMockConnection(array('fetchObject'));
    $conn->expects($this->once())
         ->method('fetchObject')
         ->with($this->equalTo('SELECT id, name FROM users WHERE id = ? LIMIT 1'), array(1));
    $conn->find('users', array('id', 'name'), array('id' => 1));
  }


  public function testFindAll()
  {
    $queries = array(
      array(),
      array('SELECT * FROM users'),

      array(array('id', 'name')),
      array('SELECT id, name FROM users'),

      array(array('id' => 1)),
      array('SELECT * FROM users WHERE id = ?', array(1)),

      array(array(), array('id' => 1)),
      array('SELECT * FROM users WHERE id = ?', array(1)),

      array(array('id', 'name'), array('id' => 1)),
      array('SELECT id, name FROM users WHERE id = ?', array(1)),

      array(array('id', 'name'), array('id' => 1), 10),
      array('SELECT id, name FROM users WHERE id = ? LIMIT 10', array(1)),

      array(array('id' => 1), 10),
      array('SELECT * FROM users WHERE id = ? LIMIT 10', array(1)),

      array(array('id', 'name'), array('id' => 1), 10, 20),
      array('SELECT id, name FROM users WHERE id = ? LIMIT 10 OFFSET 20', array(1))
    );

    for ($i=0; $i<count($queries); $i+=2) {
      $query  = $queries[$i];
      $result = $queries[$i+1];

      $conn = $this->getMockConnection(array('fetchAll'));
      $mock = $conn->expects($this->once())->method('fetchAll');
      call_user_func_array(array($mock, 'with'), $result);

      array_unshift($query, 'users');
      call_user_func_array(array($conn, 'findAll'), $query);
    }
  }
}
