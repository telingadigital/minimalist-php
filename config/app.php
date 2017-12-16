<?php

$auth       = require_once __DIR__ . '/auth.php';
$database   = require_once __DIR__ . '/database.php';
$filesystem = require_once __DIR__ . '/filesystem.php';
$mail       = require_once __DIR__ . '/mail.php';
$services   = require_once __DIR__ . '/services.php';
$session    = require_once __DIR__ . '/session.php';
$view       = require_once __DIR__ . '/view.php';

return [

    'name'        => getenv('APP_NAME') ?: 'MVP',
    'environment' => getenv('APP_ENV') ?: 'production',
    'debug'       => getenv('APP_DEBUG') ?: false,
    'url'         => getenv('APP_URL') ?: 'http://localhost',
    'timezone'    => 'utc',
    'locale'      => 'en',
    'log'         => 'single',
    'log_level'   => 'debug',
    'auth'        => $auth,
    'database'    => $database,
    'filesystem'  => $filesystem,
    'mail'        => $mail,
    'services'    => $services,
    'session'     => $session,
    'view'        => $view,

    // Configure Twig
    Twig_Environment::class => function() use($view){
        $loader = new Twig_Loader_Filesystem(
            $view['paths']
        );
        $twig = new Twig_Environment($loader);
        return $twig;
    },

    // Configure Logger
    Logger::class => function() {
        $logger = new \Monolog\Logger('minimalist_php');
        $logger->pushHandler(
            new StreamHandler(__DIR__.'/../all.log')
        );
        
        $logger->pushHandler(
            new StreamHandler(
                __DIR__.'/../error.log',
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
