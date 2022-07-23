<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use Hyperf\Database\Connection;
use Hyperf\Database\Connectors\ConnectionFactory;

return [
    'scan' => [
        'paths' => [
            BASE_PATH . '/app',
        ],
        'ignore_annotations' => [
            'mixin',
        ],
        'class_map' => [
            Hyperf\Utils\Coroutine::class => BASE_PATH . '/app/Kernel/ClassMap/Coroutine.php',
            Hyperf\Di\Resolver\ResolverDispatcher::class => BASE_PATH . '/app/Kernel/ClassMap/ResolverDispatcher.php',
            // TODO 这里在 ConfigProvider 替换无效
            \Hyperf\Database\Connectors\Connector::class => BASE_PATH . '/vendor/hyperf/seata/src/Rm/DataSource/Connector.php',
        ],
    ],
];
