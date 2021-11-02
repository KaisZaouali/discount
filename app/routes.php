<?php
declare(strict_types=1);

use App\Application\Actions\Product\ListProductsAction;
use App\Application\Actions\Product\ViewProductAction;
use App\Application\Actions\Discount\GetDiscountAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/products', function (Group $group) {
        $group->get('', ListProductsAction::class);
        $group->get('/{id}', ViewProductAction::class);
    });

    $app->group('/discount', function (Group $group) {
        $group->post('', GetDiscountAction::class);
    });
};
