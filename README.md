# Schemas [![Build Status](https://travis-ci.org/PlasmaPHP/schemas.svg?branch=master)](https://travis-ci.org/PlasmaPHP/schemas) [![Build Status](https://scrutinizer-ci.com/g/PlasmaPHP/schemas/badges/build.png?b=master)](https://scrutinizer-ci.com/g/PlasmaPHP/schemas/build-status/master) [![Code Coverage](https://scrutinizer-ci.com/g/PlasmaPHP/schemas/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/PlasmaPHP/schemas/?branch=master)

Schemas is a simple Object Relational Mapper (ORM) for Plasma. Schemas maps any data source into a PHP object.

# Getting Started
Schemas can be installed through composer.

```
composer require plasma/schemas
```

You first need to create a Plasma client and then create a `Repository` (which acts like a client) with the created client.
Then you need to create your schema classes and the schema builders for these schema classes. You will need to register these builders to the Repository.

After that, each call onto the Repository `query` or `execute` methods will give you a dedicated `SchemaCollection` with the `Schema` instances.
A call to `Repository::prepare` will give you, if successful, a `Statement` with `execute` method which acts like the Repository's `execute` method.

```php
$loop = \React\EventLoop\Factory::create();
$factory = new \Plasma\Drivers\MySQL\DriverFactory($loop);

$client = \Plasma\Client::create($factory, 'root:1234@localhost');
$repository = new \Plasma\Schemas\Repository($client);

class A implements \Plasma\Schemas\SchemaInterface {}

$builderA = new \Plasma\Schemas\SchemaBuilder('A'); // Schema "A" for the database table "table_a"
$repository->registerSchemaBuilder('table_a', $builderA);

$repository->execute('SELECT * FROM `table_a`', array())
    ->done(function (\Plasma\Schemas\SchemaCollection $collection) {
        // Do something with the collection
    });

$loop->run();
```

# Documentation
https://plasmaphp.github.io/schemas/
