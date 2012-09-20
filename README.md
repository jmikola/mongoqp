mongoqp
=======

Frontend for MongoDB's query profiler collections, built using [Silex][1].

## Setup

### Install Dependencies

    $ composer.phar install

### Configuration

The `src/` directory includes a `config.php.dist` file, which should be copied
to `config.php`. The cache directory for Twig may be customized.

### Cache Directory

Create the cache directory (`cache/` by default) and ensure it is writable by
your web server.

  [1]: http://silex.sensiolabs.org/
