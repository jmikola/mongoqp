<?php

namespace MongoQP\Silex\Provider;

use MongoQP\QueryProfiler;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class QueryProfilerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        if (!isset($app['mongo'])) {
            $app['mongo'] = function() { return new \MongoClient(); };
        }

        $app['query.profiler'] = function() use ($app) {
            $jsDir = __DIR__.'/../../../js';
            $code = [
                'map'      => new \MongoCode(file_get_contents($jsDir.'/map.js')),
                'reduce'   => new \MongoCode(file_get_contents($jsDir.'/reduce.js')),
                'finalize' => new \MongoCode(file_get_contents($jsDir.'/finalize.js')),
                'skeleton' => new \MongoCode(file_get_contents($jsDir.'/skeleton.js')),
            ];

            return new QueryProfiler($app['mongo'], $code);
        };
    }
}
