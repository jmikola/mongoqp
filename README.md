mongoqp
=======

**mongoqp** is a frontend for MongoDB's [query profiler][1] collections (i.e.
`db.system.profile`), built using [Silex][2] and PHP 5.4.

It currently supports:

 * Toggling query profiler levels (off, slow, all) per database
 * Grouping similar queries by BSON structure
 * Reporting aggregate query statistics (min, max, average, times)
 * Sorting, pagination and filtering via [DataTables][3]

Future plans:

 * Control over slow query thresholds
 * Improving analytics
 * Persistent data collection
 * Integration with Justin Hileman's [Genghis][4] (single-file MongoDB admin)
 * Integration with Tyler Brock's [mongo-hacker][5] (MongoDB shell enhancements)

### Screenshots

![Server view](http://i.imgur.com/5EZbm.png)

![Database view](http://i.imgur.com/pXLc4.png)

## Setup

### Installation

Dependencies are managed with [Composer][6], a PHP package manager. This project
is also published as a package, which means it can be installed with:

    $ composer.phar create-project jmikola/mongoqp

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
driver's [connection documentation][7] for additional examples on connecting to
a replica set or shard cluster.

#### Cache Directory

By default, the application will use `mongoqp-cache/` within the system's
temporary directory. This path, which must be writable, may be customized via
the `twig.cache_dir` configuration option.

### Web Server

The application can be started using:

    $ php -S localhost:8080 -t web web/index.php

Instructions for other web server configurations are outlined in the
[Silex documentation][8].

  [1]: http://www.mongodb.org/display/DOCS/Database+Profiler
  [2]: http://silex.sensiolabs.org/
  [3]: http://datatables.net/
  [4]: https://github.com/bobthecow/genghis
  [5]: https://github.com/TylerBrock/mongo-hacker
  [6]: http://getcomposer.org/
  [7]: http://php.net/manual/en/mongo.connecting.php
  [8]: http://silex.sensiolabs.org/doc/web_servers.html
