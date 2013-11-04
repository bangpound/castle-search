<?php

// Cache
$app['cache.path'] = realpath(__DIR__ . '/../cache');

// Http cache
$app['http_cache.cache_dir'] = $app['cache.path'] . '/http';

// Twig cache
$app['twig.options.cache'] = $app['cache.path'] . '/twig';

// Elasticsearch
$app['elasticsearch.prod.host'] = 'http://localhost:9200';
$app['elasticsearch.prod.index'] = 'production';
