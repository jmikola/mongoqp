<?php

namespace MongoQP\Silex\Provider;

use MongoDB\Client;
use MongoQP\QueryProfiler;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class QueryProfilerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['mongodb.client'] = function() use ($app) {
            return new Client(
                $app['mongodb.client.uri'],
                $app['mongodb.client.uriOptions'],
                $app['mongodb.client.driverOptions']
            );
        };

        $app['query.profiler'] = function() use ($app) {
            $jsDir = __DIR__.'/../../../js';
            $code = [
                'map'      => file_get_contents($jsDir.'/map.js'),
                'reduce'   => file_get_contents($jsDir.'/reduce.js'),
                'finalize' => file_get_contents($jsDir.'/finalize.js'),
                'skeleton' => file_get_contents($jsDir.'/skeleton.js'),
            ];

            return new QueryProfiler($app['mongodb.client'], $code);
        };
    }
}
