<?php

use MongoQP\Silex\Provider\QueryProfilerServiceProvider;
use MongoQP\Twig\MongoQPExtension;
use Silex\Application;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Response;

$loader = require_once __DIR__.'/../vendor/autoload.php';

$app = new Application();

require_once is_file(__DIR__.'/config.php')
    ? __DIR__.'/config.php'
    : __DIR__.'/config.php.dist';

$app->register(new QueryProfilerServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app->register(new TwigServiceProvider(), [
    'twig.options' => ['cache' => $app['twig.cache_dir']],
    'twig.path'    => __DIR__.'/views',
]);

$app['twig'] = $app->extend('twig', function($twig, $app) {
    $twig->getExtension('core')->setDateFormat('c');
    $twig->getExtension('core')->setTimezone(ini_get('date.timezone'));
    $twig->addExtension(new MongoQPExtension());

    return $twig;
});

require_once __DIR__.'/controllers.php';

return $app;
