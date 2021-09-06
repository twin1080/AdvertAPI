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
        ->constructorParameter(
            'databaseName',
            DI\env('ADS_MONGO_DATABASE', 'test')),
    MongoClient::class => create(MongoClient::class)
        ->constructor(
            DI\env('ADS_MONGO_URL', 'mongodb://localhost:27017/')),
];