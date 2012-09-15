Simple table data gateway built on Doctrine DBAL.

```php
$connection = Doctrine\DBAL\DriverManager::getConnection(array(
  'pdo' => new \PDO('sqlite::memory:'),
  'wrapperClass' => 'Tabler\\Connection',
  'namespace' => 'App'
));

# SELECT * FROM users WHERE id = 1
$connection->find('users', ['id' => 1]);

# SELECT id, name FROM users WHERE id = 1
$connection->find('users', ['id', 'name'], ['id' => 1]);

# SELECT * FROM users
$connection->findAll('users');

# SELECT * FROM users WHERE gender = 'm' LIMIT 10
$connection->findAll('users', ['gender' => 'm'], 10);
```

Creates new classes under "namespace" by array access.
New objects are injected the Connection instance.

```php
# new App\Users($connection)
$users = $connection['users'];

# SELECT * FROM users WHERE id = 1
$users->find(['id' => 1]);
```
