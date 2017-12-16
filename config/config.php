<?php

use function DI\object;
use Monolog\ErrorHandler;
use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface as Logger;

$config = require_once __DIR__ .'/app.php';

return [
	// Configure Twig
    Twig_Environment::class => function() {
        $loader = new Twig_Loader_Filesystem(
        	$config['view']['paths']
        );
        $twig = new Twig_Environment($loader);
        $twig->addGlobal('app',$config['app']);
        return $twig;
    },

    // Configure Logger
    Logger::class => function() {
        $logger = new \Monolog\Logger('minimalist_php');
        $logger->pushHandler(
            new StreamHandler(__DIR__. '/../logs/all.log')
        );
        
        $logger->pushHandler(
            new StreamHandler(
                __DIR__.'/../logs/error.log',
                \Monolog\Logger::NOTICE
            )
        );
        
        if (getenv('APP_ENV') == 'development') {
            $logger->pushHandler(new BrowserConsoleHandler());
        }

        ErrorHandler::register($logger);
        $logging->info('logging set up');
        return $logger;
    },
];