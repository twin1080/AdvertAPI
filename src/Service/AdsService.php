<?php
declare(strict_types=1);

namespace AdvertAPI\Service;

use AdvertAPI\Exception\APILogicException;
use AdvertAPI\Model\Advert;
use MongoDB\Client as MongoClient;
use MongoDB\Database;

class AdsService
{
    private MongoClient $client;

    private Database $database;

    public function __construct(MongoClient $client, $databaseName)
    {
        $this->client = $client;
        $this->database = $this->client->selectDatabase($databaseName);
    }

    public function create(Advert $advert) {
        $options =
            [
                'typeMap' => ['root' => Advert::class],
                'sort'=> ['id' => -1],
                'limit' => 1,
                'projection' => ['id' => 1]
            ];

        $id = 1;

        foreach ($this->database->ads->find([], $options) as $item) {
            $id+=$item->getId();
        }

        $advert->setId($id);
        $this->database->ads->insertOne($advert);
        return $advert;
    }

    public function getMostRelevantAd()
    {
        $lastShownAdvert = $this->database->ads->findOne(
            ['shownCount' => ['$gt' => 0]],
            ['sort' => ['lastShowTime' => -1]]);

        $filter = ['$where' => 'this.limit > this.shownCount'];
        if ($lastShownAdvert) {
            $filter['id'] = ['$ne' => $lastShownAdvert->getId()];
        }
        $options = ['sort' => ['price' => -1, 'lastShowTime' => 1]];
        foreach($this->database->ads->find($filter, $options) as $relevant) {
            return $relevant;
        }

        $filter = ['$where' => 'this.limit > this.shownCount'];
        return $this->database->ads->findOne($filter, $options);
    }

    public function update(Advert $advert)
    {
        $filter = ['id' => $advert->getId()];
        if ($this->database->ads->countDocuments($filter)) {
            $this->database->ads->findOneAndUpdate($filter, ['$set' => $advert]);
        } else {
            throw new APILogicException('Not found object with id = ' . $advert->getId(), 400);
        }
    }
}