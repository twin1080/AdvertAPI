<?php
declare(strict_types=1);

use AdvertAPI\AdsController;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

return simpleDispatcher(function (RouteCollector $r) {
    $r->post('/ads', [AdsController::class, 'createAction']);
    $r->post('/ads/{id:\d+}', [AdsController::class, 'updateAction']);
    $r->get('/ads/relevant', [AdsController::class, 'relevantAction']);
});
