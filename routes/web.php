<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix'=>'api/v1'], function() use($router){

    $router->get('/get-budget-totals/{allocationType}', 'StatisticsController@getBudgetTotalsByCategory');
    $router->get('/get-all-funds/{allocationType}/{schoolId}', 'StatisticsController@getBudgetBalance');
    $router->get('/get-fund-list/{allocationType}', 'StatisticsController@getListOfAllFunds');
    $router->get('/get-total-spent/{allocationType}', 'StatisticsController@getTotalSpentFunds');
    $router->get('/get-remaining-balance/{allocationType}', 'StatisticsController@getRemainingBalance');

});
