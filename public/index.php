<?php

declare(strict_types=1);

use Epignosis\Academy\Security\TemplateMiddleware;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, false, false);

$app->group('', function (RouteCollectorProxy $group){
    // Ex01 - CSRF
    $group->group('/ex01', function (RouteCollectorProxy $group){
        // App
        $group->get('/', '\Epignosis\Academy\Security\Ex01CSRF:home');
        $group->get('/add', '\Epignosis\Academy\Security\Ex01CSRF:add');
        $group->get('/del/{id:[0-9]+}', '\Epignosis\Academy\Security\Ex01CSRF:delete');

        // 1337
        $group->get('/leet', '\Epignosis\Academy\Security\Ex01CSRF:leet');
    });

    // Ex02 - SQL injection
    $group->group('/ex02', function (RouteCollectorProxy $group){
        // App
        $group->get('/', '\Epignosis\Academy\Security\Ex02Injection:home');
        $group->get('/del/{id}', '\Epignosis\Academy\Security\Ex02Injection:delete');
    });

    // Ex03 - Persistent XSS
    $group->group('/ex03', function (RouteCollectorProxy $group){
        // App
        $group->get('/', '\Epignosis\Academy\Security\Ex03XSS:home');
        $group->post('/add', '\Epignosis\Academy\Security\Ex03XSS:add');

        // 1337
        $group->post('/leet', '\Epignosis\Academy\Security\Ex03XSS:leet');
    });
})->add(new TemplateMiddleware());

$app->run();
