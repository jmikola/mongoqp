<?php

namespace MongoQP\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use MongoQP\QueryProfiler;

class QueryProfilerServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        if (!isset($app['mongo'])) {
            $app['mongo'] = $app->share(function() { return new \MongoClient(); });
        }

        $app['query.profiler'] = $app->share(function () use ($app) {
            $jsDir = __DIR__.'/../../../js';
            $code = [
                'map'      => new \MongoCode(file_get_contents($jsDir.'/map.js')),
                'reduce'   => new \MongoCode(file_get_contents($jsDir.'/reduce.js')),
                'finalize' => new \MongoCode(file_get_contents($jsDir.'/finalize.js')),
                'skeleton' => new \MongoCode(file_get_contents($jsDir.'/skeleton.js')),
            ];

            return new QueryProfiler($app['mongo'], $code);
        });
    }

    public function boot(Application $app)
    {
    }
}
