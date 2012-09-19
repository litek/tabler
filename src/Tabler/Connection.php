<?php
namespace Tabler;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Configuration;
use Doctrine\Common\EventManager;

class Connection extends \Doctrine\DBAL\Connection implements \ArrayAccess
{
  protected $tables = array();


  /**
   * Check if table is loaded or if class exists
   *
   * @todo Should we return true when $this->tables[$key] exists, or just when the class exists?
   * @param string
   */
  public function offsetExists($key)
  {
    return isset($this->tables[$key]) or class_exists($this->getTableClass($key));
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
        $params  = $this->getParams();
        $tableClass = isset($params['table_class']) ? $params['table_class'] : 'Tabler\Table';
        $this->tables[$key] = new $tableClass($this, $key);
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
    return $this->fetchAssoc($sql, array_values($identifier));
  }


  /**
   * Find multiple rows
   *
   * @param string $table
   * @param array $columns
   * @param array $identifier The find criteria. An associative array containing column-value pairs.
   * @param array $options
   * @return array
   */
  public function findAll($table, array $columns = array(), array $identifier = array(), array $options = array())
  {
    if (empty($columns)) {
      $columns = array('*');
    }

    if (!isset($columns[0])) {
      $options = $identifier;
      $identifier = $columns;
      $columns = array('*');
    }

    $fields = implode($columns, ', ');
    $where  = !empty($identifier) ? ' WHERE '.implode(' = ? AND ', array_keys($identifier)).' = ?' : '';
    $order  = isset($options['order']) ? ' ORDER BY'.$options['order'] : '';
    $limit  = isset($options['limit']) ? sprintf(' LIMIT %d', $options['limit']) : '';
    $offset = isset($options['limit']) && isset($options['offset']) ? sprintf(' OFFSET %d', $options['offset']) : '';

    $sql = 'SELECT '.$fields.' FROM '.$table.$where.$order.$limit.$offset;
    return $this->fetchAll($sql, array_values($identifier));
  }
}
