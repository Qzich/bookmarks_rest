<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;
use \Symfony\Component\HttpKernel;
use Rest\Framework;
use Symfony\Component\Routing\Loader\PhpFileLoader;

$request = Request::createFromGlobals();

$router = new Routing\Router(
    new PhpFileLoader(new FileLocator(__DIR__.'/../src/')), 'app.php'
);

$router->setContext((new Routing\RequestContext())->fromRequest($request));

$framework = new Framework($router, new HttpKernel\Controller\ControllerResolver(), new HttpKernel\Controller\ArgumentResolver());

/** @var Response $response */
$response = $framework->handle($request);
$response->send();