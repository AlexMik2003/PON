<?php

define('ROOT_PATH',realpath(__DIR__ . "/.."));

require_once ROOT_PATH . '/vendor/autoload.php';

use \App\Helpers\Session;

Session::init();

$config = require_once ROOT_PATH."/bootstrap/config.php";

$app = new \Slim\App($config);

$container = $app->getContainer();

$container["view"] = function ($container)
{
    $view = new \Slim\Views\Twig(ROOT_PATH."/resources/views",[
        "cache" => false,
    ]);

    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    $view->getEnvironment()->addGlobal("user" ,$container->get("auth")->check());
    $view->getEnvironment()->addGlobal("flash",$container->get("flash"));

    return $view;

};

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container["db"]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container["db"] = function ($container) use ($capsule)
{
    return $capsule;
};

$container["index"] = function ($container)
{
    return new \App\Controllers\IndexController($container);
};

$container["bdcom"] = function ($container)
{
    return new \App\Controllers\BDCOM\BdcomController($container);
};

$container["command"] = function ($container)
{
    return new \App\Controllers\Command\CommandController();
};

$container["device_config"] = function ()
{
    return new \App\Controllers\Command\CommandController();
};

$container["ip"] = function ()
{
    return new \App\Helpers\IP();
};

$container["authorized"] = function ($container)
{
    return new \App\Controllers\User\AuthController($container);
};

$container["auth"] = function ($container)
{
    return new App\Auth\Auth;
};

$container["validator"] = function ($container)
{
    return new \App\Validation\Validator;
};

$container["csrf"] = function ($container)
{
    return new \Slim\Csrf\Guard;
};

$container["flash"] = function ($container){
    return new \Slim\Flash\Messages;
};


$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));

$app->add($container->csrf);

\Respect\Validation\Validator::with('App\\Validation\\Rules');

require_once ROOT_PATH."/app/routes.php";


