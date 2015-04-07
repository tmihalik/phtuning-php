## Query builder for raw SQL

Using with simple tables:
```php
$query = new \Phalcon\Db\Query\Builder;
$query->columns('name')
    ->from('robots')
    ->andWhere('age > :age:')
    ->orderBy('name, id');
$result = $query->execute(['age' => 18]);
$result->setFetchMode(\Phalcon\Db::FETCH_ASSOC);

while ($row = $result->fetch()) {
  echo $row['name'];
}
```

Using with stored procedures:
```php
$query = new \Phalcon\Db\Query\Builder;
$query->columns('name')
    ->from('getrobotsbyage(:age:)')
    ->orderBy('name, id');
$result = $query->execute(['age' => 18]);
$result->setFetchMode(\Phalcon\Db::FETCH_ASSOC);

while ($row = $result->fetch()) {
  echo $row['name'];
}
```
