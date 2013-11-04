<?php

use Silex\Application;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\HttpFoundation\RequestMatcher;

$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new Bangpound\Guzzle\Proxy\GuzzleProxyProvider(), array(
    'proxy.endpoints' => array(
        'prod' => array(
            'host' => $app['elasticsearch.prod.host'] . '/' . $app['elasticsearch.prod.index'],
        ),
    ),
    'proxy.mount_prefix' => '/search',
));

$app->register(new Bangpound\Silex\PhpCmsBootstrapProvider(), array());
$app->register(new Bangpound\Silex\WordpressServiceProvider(), array());

$app->register(new TwigServiceProvider(), array(
    'twig.options'        => array(
        'cache'            => isset($app['twig.options.cache']) ? $app['twig.options.cache'] : false,
        'strict_variables' => true
    ),
    'twig.path'    => array(__DIR__.'/../templates'),
));

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addGlobal('google', ['analytics' => ['trackingId' => 'UA-23288804-4']]);

    return $twig;
}));

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
    'http_cache.esi'       => null,
    'http_cache.cache_dir' => $app['http_cache.cache_dir'],
));

if ($app['debug'] && isset($app['cache.path'])) {
    $app->register(new \Silex\Provider\MonologServiceProvider(), array(
        'monolog.logfile' => __DIR__.'/../logs/silex_dev.log',
    ));

    $app->register(new \Silex\Provider\WebProfilerServiceProvider(), array(
        'profiler.cache_dir' => __DIR__.'/../cache/profiler',
    ));

    $app['ladybug'] = $app->share(function () use ($app) {
        return \Ladybug\Dumper::getInstance();
    });
}

return $app;
