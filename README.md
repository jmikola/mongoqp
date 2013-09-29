mongoqp
=======

**mongoqp** is a frontend for MongoDB's [query profiler][] collections (i.e.
`db.system.profile`), built using [Silex][] and PHP 5.4.

It currently supports:

 * Toggling query profiler levels (off, slow, all) per database
 * Grouping similar queries by BSON structure
 * Reporting aggregate query statistics (min, max, average, times)
 * Sorting, pagination and filtering via [DataTables][]

Future plans:

 * Control over slow query thresholds
 * Improving analytics
 * Persistent data collection
 * Integration with Justin Hileman's [Genghis][] (single-file MongoDB admin)
 * Integration with Tyler Brock's [mongo-hacker][] (MongoDB shell enhancements)

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
 * `twig.cache_dir`: Cache directory for Twig templates

#### Database Connection

By default, the application will connect to a standalone MongoDB server on the
local host (i.e. `new MongoClient()`). The connection may be customized by
defining a shared `mongo` service in `config.php`:

```php
$app['mongo'] = $app->share(function() {
    return new \MongoClient('mongodb://example.com:27017');
});
```

The above example connects to a standalone server by its hostname. Consult the
PHP driver's [connection documentation][] for additional examples on connecting
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
$ php -S localhost:8080 -t web web/index.php
```

Instructions for other web server configurations are outlined in the
[Silex documentation][].

  [query profiler]: http://docs.mongodb.org/manual/tutorial/manage-the-database-profiler/
  [Silex]: http://silex.sensiolabs.org/
  [DataTables]: http://datatables.net/
  [Genghis]: https://github.com/bobthecow/genghis
  [mongo-hacker]: https://github.com/TylerBrock/mongo-hacker
  [Composer]: http://getcomposer.org/
  [connection documentation]: http://php.net/manual/en/mongo.connecting.php
  [Silex documentation]: http://silex.sensiolabs.org/doc/web_servers.html
