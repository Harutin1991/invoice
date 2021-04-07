<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix'=>'api/v1'], function() use($router){

    $router->get('/item', 'InvoiceController@index');
    $router->get('/item/{id}', 'InvoiceController@getInvoiceItem');
    $router->get('/invoice-status', 'InvoiceController@getInvoiceStatuses');
    $router->get('/payment-status', 'InvoiceController@getPaymentStatuses');
    $router->get('/payment-type', 'InvoiceController@getPaymentType');
    $router->get('/invoice-type', 'InvoiceController@getInvoiceType');
    $router->get('/invoice-term', 'InvoiceController@getTerms');
    $router->get('/get-budget-list/{allocationType}', 'InvoiceController@getBudgetList');

    $router->post('/add-invoice/{allocationType}', 'InvoiceController@addInvoice');
});
