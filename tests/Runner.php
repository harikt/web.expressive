<?php

namespace Dms;

use Dms\Common\Testing;
use Dms\Core\Tests\Helpers\Comparators\ObjectCollectionComparator;
use SebastianBergmann\Comparator\Factory;

$projectAutoLoaderPath    = __DIR__ . '/../vendor/autoload.php';
$dependencyAutoLoaderPath = __DIR__ . '/../../../../autoload.php';

if (file_exists($projectAutoLoaderPath)) {
    $composerAutoLoader = require $projectAutoLoaderPath;
} elseif (file_exists($dependencyAutoLoaderPath)) {
    $composerAutoLoader = require $dependencyAutoLoaderPath;
} else {
    throw new \Exception('Cannot load tests for ' . __NAMESPACE__ . ' under ' . __DIR__ . ': please load via composer');
}

$composerAutoLoader->addPsr4(__NAMESPACE__ . '\\', __DIR__);

// Factory::getInstance()->register(new ObjectCollectionComparator());
// Testing\Bootstrapper::run(__NAMESPACE__, dirname(__DIR__), 'phpunit.xml.dist');
