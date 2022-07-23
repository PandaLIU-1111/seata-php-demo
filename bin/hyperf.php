#!/usr/bin/env php
<?php

use function Swow\Tools\br;

ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Asia/Shanghai');

! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));
! defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', 0);

require BASE_PATH . '/vendor/autoload.php';

// Self-called anonymous function that creates its own scope and keep the global namespace clean.
(function () {
    Hyperf\Di\ClassLoader::init();
//    \Swow\Debug\registerExtendedStatementHandler(function () {
//        $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1)[0];
//        $dump = true;
//        if (str_contains($bt['file'], 'vendor')) {
//            if (! str_contains($bt['file'], 'hyperf')) {
//                $dump = false;
//            }
//        }
//        if ($dump) {
//            $file = explode("\n", file_get_contents(($bt['file'])));;
//            echo trim($file[$bt['line'] - 1]) . "\t" . $bt['file'] . ":" . $bt['line'] . PHP_EOL;
//        }
//    });
//    \Swow\Debug\Debugger::runOnTTY();
    /** @var Psr\Container\ContainerInterface $container */
    $container = require BASE_PATH . '/config/container.php';
    /** @var Symfony\Component\Console\Application $application */
    $application = $container->get(Hyperf\Contract\ApplicationInterface::class);
    $application->run();
})();

