<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

$file = __DIR__.'/../vendor/autoload.php';
if (!file_exists($file)) {
    throw new RuntimeException('Install dependencies to run test suite.');
}

/**
 * @var ClassLoader $loader
 */
$loader = require $file;
$loader->add('TreeHouse\Queue\Tests', __DIR__ . '/TreeHouse/Queue/Tests');

AnnotationRegistry::registerLoader([$loader, 'loadClass']);
