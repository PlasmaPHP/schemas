# Schemas [![Build Status](https://travis-ci.org/PlasmaPHP/schemas.svg?branch=master)](https://travis-ci.org/PlasmaPHP/schemas) [![Build Status](https://scrutinizer-ci.com/g/PlasmaPHP/schemas/badges/build.png?b=master)](https://scrutinizer-ci.com/g/PlasmaPHP/schemas/build-status/master) [![Code Coverage](https://scrutinizer-ci.com/g/PlasmaPHP/schemas/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/PlasmaPHP/schemas/?branch=master)

Schemas is a simple Object Relational Mapper (ORM) for Plasma. Schemas maps any data source into a PHP object.

# Getting Started
Schemas can be installed through composer.

```
composer require plasma/schemas
```

You first need to create a Plasma client and then create a `Repository` (which acts like a client) with the created client.
Then you need to create your schema classes and the directory for these schema classes. You will need to register these directories to the Repository.

Directories build schemas from query results and interface with the repository for queries.

After that, each call onto the Repository `query` or `execute` methods will give you a dedicated `SchemaCollection` with the `Schema` instances.
A call to `Repository::prepare` will give you, if successful, a wrapped `Statement` instance. The wrapper has the same purpose as the `Repository`.

```php
$loop = \React\EventLoop\Factory::create();
$factory = new \Plasma\Drivers\MySQL\DriverFactory($loop, array());

$client = \Plasma\Client::create($factory, 'root:1234@localhost');
$repository = new \Plasma\Schemas\Repository($client);

/**
 * Our example table "users" consists of two columns:
 * - id ; auto incremented integer (length 12) primary
 * - name ; varchar(255) utf8mb4_generl_ci
 */
class Users extends \Plasma\Schemas\SQLSchema {
    public $id;
    public $name;
    
    /**
     * Returns the schema definition.
     * @return \Plasma\Schemas\SQLColumnDefinitionInterface[]
     */
    static function getDefinition(): array {
        return array(
            // A generic column definition builder
            // solely for ease of use and does not
            // have to be used.
            // Any Plasma Column Definition
            // can be used.
            
            static::getColDefBuilder()
                ->name('id')
                ->type('INTEGER')
                ->length(12)
                ->autoIncrement()
                ->primary()
                ->getDefinition(),
            static::getColDefBuilder()
                ->name('name')
                ->type('VARCHAR')
                ->length(255)
                ->getDefinition()
        );
    }
    
    /**
     * Returns the name of the table.
     * @return string
     */
    static function getTableName(): string {
        return 'users';
    }
    
    /**
     * Returns the name of the identifier column (primary or unique), or null.
     * @return string|null
     */
    static function getIdentifierColumn(): ?string {
        return 'id';
    }
}

// null is the SQL grammar (see plasma/sql-common)
$builderA = new \Plasma\Schemas\SQLDirectory(Users::class, null);
$repository->registerDirectory('users', $builderA);

$repository->execute('SELECT * FROM `users`', array())
    ->done(function (\Plasma\Schemas\SchemaCollection $collection) {
        // Do something with the collection
    });

$loop->run();
```

# Preloads
Schemas has a mechanism called Preloads.

Preloads are a way to load foreign references at the same time as a schema gets loaded, and let your schema be always filled with the foreign reference schema.
How the preloads are exactly loaded depends on the Directory implementation.

Preloads are foreign targets with fetch mode `ALWAYS` and are automatically handled.
Foreign target with fetch mode `LAZY` are not automatically loaded and need to be explicitely asked for by calling `getAsyncResolver` on the schema.

Whether one uses one over the other fetch mode depends on the use case. It makes sense to only preload schemas you actually really always need.

Preloads are supported through the `ColumnDefinitionInterface`. Current implementations are the `ColumnDefinition` implementation and the `ColumnDefinitionBuilder`.

# Documentation
https://plasmaphp.github.io/schemas/
