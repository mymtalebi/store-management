<?php

/**
 * This file is part of Store Management project.
 *
 * (c) Maryam Talebi <mym.talebi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file readme.md.
 */

/**
 * Here is where you can register all of the routes for an application.
 * It is a breeze. Simply tell Lumen the URIs it should respond to
 * and give it the Closure to call when that URI is requested.
 */
$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group([
    'prefix' => 'store',
], function () use ($router) {
    $router->get('', ['uses' => 'StoreController@getAll']);

    $router->get('{id}', ['uses' => 'StoreController@get']);

    $router->post('', ['uses' => 'StoreController@create']);

    $router->put('{id}', ['uses' => 'StoreController@update']);

    $router->delete('{id}', ['uses' => 'StoreController@delete']);
});
