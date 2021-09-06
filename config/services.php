<?php
declare(strict_types=1);

use AdvertAPI\AdsController;
use AdvertAPI\Service\AdsService;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;
use MongoDB\Client as MongoClient;
use function DI\create;

return [
    AdsService::class => DI\autowire(AdsService::class)
        ->constructorParameter('databaseName', 'test'),
    MongoClient::class => create(MongoClient::class)
        ->constructor('mongodb://0.0.0.0:27017/'),

//    AdsController::class => create(AdsController::class),
//    ServerRequestInterface::class => ServerRequestFactory::fromGlobals(),
];