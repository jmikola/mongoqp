mongoqp
=======

**mongoqp** is a frontend for MongoDB's [query profiler][] collections (i.e.
`db.system.profile`), built using [Silex][] and [MongoDB PHP Library][].

It currently supports:

 * Toggling query profiler levels (off, slow, all) per database
 * Grouping similar queries by BSON structure
 * Reporting aggregate query statistics (min, max, average, times)
 * Sorting, pagination and filtering via [DataTables][]

Future plans:

 * Control over slow query thresholds
 * Improving analytics
 * Persistent data collection

### Screenshots

![Server view](http://i.imgur.com/5EZbm.png)

![Database view](http://i.imgur.com/pXLc4.png)

## Setup

### Installation

Dependencies are managed with [Composer][], a PHP package manager. This project
is also published as a package, which means it can be installed with:

```
$ composer create-project jmikola/mongoqp
```

### Configuration

The `src/` directory includes a `config.php.dist` file, which may be copied
to `config.php` and customized. If `config.php` is not present, the default
configuration will be included.

Currently, the following options are available:

 * `debug`: Enable verbose error reporting
 * `mongodb.client.uri`: MongoDB connection URI string
 * `mongodb.client.uriOptions`: MongoDB connection URI options
 * `mongodb.client.driverOptions`: MongoDB driver options
 * `twig.cache_dir`: Cache directory for Twig templates

#### Database Connection

By default, the application will connect to a standalone MongoDB server on the
local host (i.e. `new MongoDB\Client`). The connection may be customized via the
`mongodb.client` options, like so:

```php
$app['mongodb.client.uri'] = 'mongodb://example.com:27017';
```

The above example connects to a standalone server by its hostname. Consult the
[MongoDB PHP library documentation][] for additional examples on connecting
to a replica set or specifying auth credentials.

Database profiling cannot be enabled on `mongos` instances. If you are profiling
queries in a sharded cluster, the application should be configured to connect to
an individual shard.

#### Cache Directory

By default, the application will use `mongoqp-cache/` within the system's
temporary directory. This path, which must be writable, may be customized via
the `twig.cache_dir` configuration option.

### Web Server

The application can be started using:

```
$ php -S localhost:8080 -t web
```

Instructions for other web server configurations are outlined in the
[Silex documentation][].

  [query profiler]: https://docs.mongodb.com/manual/tutorial/manage-the-database-profiler/
  [Silex]: https://silex.sensiolabs.org/
  [MongoDB PHP Library]: https://github.com/mongodb/mongo-php-library
  [DataTables]: http://datatables.net/
  [Composer]: http://getcomposer.org/
  [MongoDB PHP library documentation]: https://docs.mongodb.com/php-library/master/reference/method/MongoDBClient__construct/#examples
  [Silex documentation]: https://silex.sensiolabs.org/doc/2.0/web_servers.html
