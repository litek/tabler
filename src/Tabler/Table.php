<?php
namespace Tabler;

abstract class Table
{
  /**
   * @var \Tabler\Connection
   */
  protected $db;

  /**
   * Constructor
   *
   * @param \Tabler\Connection
   */
  public function __construct(Connection $db)
  {
    $this->db = $db;
  }


  /**
   * Get connection
   *
   * @return \Tabler\Connection
   */
  public function getConnection()
  {
    return $this->db;
  }


  /**
   * Set connection
   *
   * @param \Tabler\Connection
   * @return \Tabler\Model
   */
  public function setConnection(Connection $db)
  {
    $this->db = $db;
    return $this;
  }


  /**
   * Inserts a table row with specified data.
   *
   * @param array $data An associative array containing column-value pairs.
   * @param array $types Types of the inserted data.
   * @return integer The number of affected rows.
   */
  public function insert(array $data, array $types = array())
  {
    return $this->db->insert($this->getTableName(), $data, $types);
  }


  /**
   * Executes an SQL UPDATE statement on table.
   *
   * @param array $data
   * @param array $identifier The update criteria. An associative array containing column-value pairs.
   * @param array $types Types of the merged $data and $identifier arrays in that order.
   * @return integer The number of affected rows.
   */
  public function update(array $data, array $identifier, array $types = array())
  {
    return $this->db->update($this->getTableName(), $data, $identifier, $types);
  }


  /**
   * Executes an SQL DELETE statement on a table.
   *
   * @param array $identifier The deletion criteria. An associative array containing column-value pairs.
   * @return integer The number of affected rows.
   */
  public function delete(array $identifier)
  {
    return $this->db->delete($this->getTableName(), $identifier);
  }


  /**
   * Find single row
   *
   * @param array $columns
   * @param array $identifier The find criteria. An associative array containing column-value pairs.
   * @return array
   */
  public function find(array $columns, array $identifier = null)
  {
    return $this->db->find($this->getTableName(), $columns, $identifier);
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
  public function findAll(array $columns)
  {
    $args = func_get_args();
    array_unshift($args, $this->getTableName());
    return call_user_func_array(array($this->db, 'findAll'), $args);
  }


  /**
   * Get table name from class
   *
   * @return string
   */
  static public function getTableName()
  {
    $class = get_called_class();
    $class = substr($class, strrpos($class, '\\')+1);
    return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $class));
  }
}
