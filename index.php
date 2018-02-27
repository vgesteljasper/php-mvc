<?php

use Core\Router as Router;
use Dotenv\Dotenv as Env;

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__ . DS);

require ROOT.'vendor'.DS.'autoload.php';

(new Env(__DIR__))->load();

$router = new Router;

$router->get('/', 'ProjectController@index');
$router->get('/project/{slug}', 'ProjectController@show');
$router->fallback('ErrorController@notFound');

$router->dispatch();
