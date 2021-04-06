<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix'=>'api/v1'], function() use($router){

    $router->get('/item', 'InvoiceController@index');
    $router->get('/item/{id}', 'InvoiceController@getInvoiceItem');

});
