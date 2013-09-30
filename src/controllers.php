<?php

use Symfony\Component\HttpFoundation\Request;

$app->get('/', function() use ($app) {
    $databaseCollections = array_flip($app['query.profiler']->getDatabases());

    foreach ($databaseCollections as $database => $_) {
        $databaseCollections[$database] = $app['query.profiler']->getCollections($database);
    }

    return $app['twig']->render('index.html.twig', ['databaseCollections' => $databaseCollections]);
})->bind('index');

$app->get('/p/{database}', function($database) use ($app) {
    $profiles = $app['query.profiler']->getProfilingData($database);
    $collections = $app['query.profiler']->getCollections($database);

    return $app['twig']->render('database.html.twig', [
        'database' => $database,
        'collections' => $collections,
        'profiles' => $profiles,
    ]);
})->bind('database_profiles');

$app->get('/p/{database}/{collection}', function($database, $collection) use ($app) {
    $profiles = $app['query.profiler']->getProfilingData($database, $collection);

    return $app['twig']->render('collection.html.twig', [
        'database' => $database,
        'collection' => $collection,
        'profiles' => $profiles,
    ]);
})->bind('collection_profiles');

$app->get('/c/{database}', function($database) use ($app) {
    $collections = $app['query.profiler']->getCollections($database);

    return $app['twig']->render('_collections.html.twig', [
        'database' => $database,
        'collections' => $collections,
    ]);
})->bind('database_collections');

$app->get('/pc/{database}', function($database) use ($app) {
    $level = $app['query.profiler']->getProfilingLevel($database);

    return $app['twig']->render('_profiling_control.html.twig', [
        'database' => $database,
        'level' => $level,
    ]);
})->bind('database_profiling');

$app->post('/pc/{database}', function(Request $request) use ($app) {
    $database = $request->get('database');
    $level = $request->get('level');

    $app['query.profiler']->setProfilingLevel($database, $level);

    return $app->json(['ok' => 1]);
})->bind('database_profiling_set');
