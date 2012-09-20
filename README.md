mongoqp
=======

**mongoqp** is a frontend for MongoDB's query profiler collections (i.e. `db.system.profile`), built using [Silex][1].

It currently supports:

 * Toggling query profiler levels (off, slow, all) per database
 * Grouping similar queries by BSON structure
 * Reporting aggregate query statistics (min, max, average, times)
 * Sorting, pagination and filtering via [DataTables][2]

### Screenshots

![Server view](http://i.imgur.com/5EZbm.png)

![Database view](http://i.imgur.com/pXLc4.png)

## Setup

### Install Dependencies

    $ composer.phar install

### Configuration

The `src/` directory includes a `config.php.dist` file, which should be copied
to `config.php`. The cache directory for Twig may be customized.

### Cache Directory

Create the cache directory (`cache/` by default) and ensure it is writable by
your web server.

### Web Server

Instructions for web server configurations are outlined in the
[Silex documentation][3].

  [1]: http://silex.sensiolabs.org/
  [2]: http://datatables.net/
  [3]: http://silex.sensiolabs.org/doc/web_servers.html
