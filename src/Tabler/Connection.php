<?php
namespace Tabler;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Configuration;
use Doctrine\Common\EventManager;

class Connection extends \Doctrine\DBAL\Connection implements \ArrayAccess
{
  protected $tables = array();


  /**
   * Initializes a new instance of the Connection class.
   *
   * @param array $params  The connection parameters.
   * @param Driver $driver
   * @param Configuration $config
   * @param EventManager $eventManager
   */
  public function __construct(array $params, Driver $driver, Configuration $config = null, EventManager $eventManager = null)
  {
    parent::__construct($params, $driver, $config, $eventManager);
    $this->setFetchMode(\PDO::FETCH_OBJ);
  }


  /**
   * Check if table is loaded or if class exists
   *
   * @param string
   */
  public function offsetExists($key)
  {
    return true;
  }


  /**
   * Retrieve or create table object
   *
   * @param string
   * @return Table
   */
  public function offsetGet($key)
  {
    if (!isset($this->tables[$key])) {
      $class = $this->getTableClass($key);
      if (class_exists($class)) {
        $this->tables[$key] = new $class($this);
      } else {
        $this->tables[$key] = new Table($this, $key);
      }
    }

    return $this->tables[$key];
  }


  /**
   * Set table object for key
   *
   * @param string
   * @param Table
   */
  public function offsetSet($key, $obj)
  {
    if (!$obj instanceof Table) {
      throw new \InvalidArgumentException("Value must be an instanceof Table");
    }
    
    $this->tables[$key] = $obj;
  }


  /**
   * Unset initialize table object
   *
   * @param string
   */
  public function offsetUnset($key)
  {
    if (isset($this->tables[$key])) {
      unset($this->tables[$key]);
    }
  }


  /**
   * Get table class
   *
   * @param string
   */
  public function getTableClass($key)
  {
    $params = $this->getParams();
    $class  = preg_replace('/_([\w])/e', "strtoupper('$1')", ucfirst($key));
    if (isset($params['namespace'])) {
      $class = $params['namespace'].'\\'.$class;
    }
    return $class;
  }


  /**
   * Prepares and executes an SQL query and returns the first row of the result
   * as an object.
   *
   * @param string $statement The SQL query.
   * @param array $params The query parameters.
   * @return object
   */
  public function fetchObject($statement, array $params = array())
  {
      return $this->executeQuery($statement, $params)->fetch(\PDO::FETCH_OBJ);
  }


  /**
   * Find single row
   *
   * @param string $table
   * @param array $columns
   * @param array $identifier The find criteria. An associative array containing column-value pairs.
   * @return array
   */
  public function find($table, array $columns, array $identifier = null)
  {
    if ($identifier === null) {
      $identifier = $columns;
      $columns = array('*');
    }

    $fields = implode($columns, ', ');
    $where  = implode(' = ? AND ', array_keys($identifier)).' = ?';

    $sql = sprintf('SELECT %s FROM %s WHERE %s LIMIT 1', $fields, $table, $where);
    return $this->fetchObject($sql, array_values($identifier));
  }


  /**
   * Find multiple rows
   *
   * @param string $table
   * @param array $columns
   * @param array $identifier The find criteria. An associative array containing column-value pairs.
   * @param integer $limit
   * @param integer $offset
   * @return array
   */
  public function findAll($table, array $columns = array())
  {
    $identifier = array();
    $count = func_num_args();
    $start = 2;

    if (!isset($columns[0])) {
      $identifier = $columns;
      $columns = array('*');
    }

    if ($count > $start) {
      $arg = func_get_arg($start);
      if (is_array($arg)) {
        $identifier = $arg;
        $start++;
      }
    }

    $limit  = $count > $start ? func_get_arg($start++) : null;
    $offset = $count > $start ? func_get_arg($start++) : null;

    $fields = implode($columns, ', ');
    $where  = !empty($identifier) ? ' WHERE '.implode(' = ? AND ', array_keys($identifier)).' = ?' : '';
    $limit  = $limit ? sprintf(' LIMIT %d', $limit) : '';
    $offset = $limit && $offset ? sprintf(' OFFSET %d', $offset) : '';

    $sql = 'SELECT '.$fields.' FROM '.$table.$where.$limit.$offset;
    return $this->fetchAll($sql, array_values($identifier));
  }
}
