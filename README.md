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

### Install Dependencies

Dependencies are managed with [Composer][6], a PHP package manager.

    $ composer.phar install

### Configuration

The `src/` directory includes a `config.php.dist` file, which should be copied
to `config.php`. The cache directory for Twig may be customized.

### Cache Directory

Create the cache directory (`cache/` by default) and ensure it is writable by
your web server.

### Web Server

The application can be started using:

    $ php -S localhost:8080 -t web web/index.php

Instructions for other web server configurations are outlined in the
[Silex documentation][7].

  [1]: http://www.mongodb.org/display/DOCS/Database+Profiler
  [2]: http://silex.sensiolabs.org/
  [3]: http://datatables.net/
  [4]: https://github.com/bobthecow/genghis
  [5]: https://github.com/TylerBrock/mongo-hacker
  [6]: http://getcomposer.org/
  [7]: http://silex.sensiolabs.org/doc/web_servers.html
