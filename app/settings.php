<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger' => [
                    'name'  => 'slim-app',
                    'path'  => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                'db' => (function () {
                    $isLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1']);
                    return [
                        'host'     => $isLocal ? '127.0.0.1' : 'mysql-dagli.alwaysdata.net',
                        'port'     => 3306,
                        'database' => $isLocal ? 'music' : 'dagli_api',   // nom exact de ta base en ligne
                        'username' => $isLocal ? 'root'  : 'dagli_api',
                        'password' => $isLocal ? ''      : 'Fleurs9500',
                        'charset'  => 'utf8mb4',
                        'flags'    => [
                            PDO::ATTR_PERSISTENT         => false,
                            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_EMULATE_PREPARES   => true,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        ],
                    ];
                })(),
            ]);
        }
    ]);
};