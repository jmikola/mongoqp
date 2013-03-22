<?php

use MongoQP\Silex\Provider\QueryProfilerServiceProvider;
use MongoQP\Twig\MongoQPExtension;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\HttpFoundation\Response;

$loader = require_once __DIR__.'/../vendor/autoload.php';

$app = new Application();

require_once is_file(__DIR__.'/config.php')
    ? __DIR__.'/config.php'
    : __DIR__.'/config.php.dist';

$app->register(new QueryProfilerServiceProvider());
$app->register(new UrlGeneratorServiceProvider());
$app->register(new TwigServiceProvider(), [
    'twig.options' => ['cache' => $app['twig.cache_dir']],
    'twig.path'    => __DIR__.'/views',
]);

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->getExtension('core')->setDateFormat('c');
    $twig->getExtension('core')->setTimezone(ini_get('date.timezone'));
    $twig->addExtension(new MongoQPExtension());

    return $twig;
}));

$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    $error = 404 == $code ? $e->getMessage() : null;

    return new Response($app['twig']->render('error.html.twig', ['error' => $error]), $code);
});

$app['twig']->getExtension('core')->setDateFormat('Y-m-d H:i:s O');
$app['twig']->getExtension('core')->setTimezone('America/New_York');//ini_get('date.timezone'));

require_once __DIR__.'/controllers.php';

return $app;
