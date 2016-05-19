Phalcon Tuning
==============

## Autoloading from the Phtuning

```php
$loader = new Phalcon\Loader();

$loader->registerNamespaces(array(
    'Phalcon' => '/path/to/phtuning/Library/Phalcon/'
));

$loader->register();
```

### QueryBuilder
* [Phalcon\Db\Query\Builder](https://github.com/tmihalik/phtuning-php/tree/master/Library/Phalcon/Db/Query) - Query builder for raw SQL
