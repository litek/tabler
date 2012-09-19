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
If no class is found, a virtual table class is created.

```php
# new App\Users($connection) if it exists
$users = $connection['users'];

# SELECT * FROM users WHERE id = 1
$users->find(['id' => 1]);
```

A Silex provider is also included and takes a namespace option in addition
to the same options as the DoctrineServiceProvider
```php
$app->register(new Tabler\Provider\DbServiceProvider, [
  'db.options' => [
    'driver' => 'pdo_sqlite',
    'path' => ':memory:',
    'namespace' => 'App\\Model'
  ]
]);
```

Example controller
```php
$app->get('/users/{id}', function($id) {
  $user = $app['db']['users']->find(['id' => $id]);
  // if there is no table class, this is equivalent to
  // $app['db']->find('users', ['id' => $id])
});
```
