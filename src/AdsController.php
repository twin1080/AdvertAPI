<?php declare(strict_types=1);

namespace AdvertAPI;

use AdvertAPI\Model\Advert;
use AdvertAPI\Service\AdsService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AdsController
{
    private AdsService $adsService;

    public function __construct(AdsService $adsService)
    {
        $this->adsService = $adsService;
    }

    public function updateAction($request): ResponseInterface
    {
        $advert = $this->parseRequest($request);
        $advert->setId((int)$request->getAttribute('id'));
        $this->adsService->update($advert);
        return $this->createResponseByAdvert($advert);
    }

    public function createAction(RequestInterface $request): ResponseInterface
    {
        $advert = $this->parseRequest($request);
        $this->adsService->create($advert);
        return $this->createResponseByAdvert($advert);
    }

    public function relevantAction(RequestInterface $request): ResponseInterface
    {
        $relevant = $this->adsService->getMostRelevantAd();
        if (!$relevant) {
            throw new \LogicException('There are nothing to show', 400);
        }

        $relevant->setLastShowTime(new \DateTime());
        $relevant->setShownCount($relevant->getShownCount() + 1);
        $this->adsService->update($relevant);
        return $this->createResponseByAdvert($relevant);
    }

    private function createResponseByAdvert(Advert $advert): JsonResponse
    {
        return new JsonResponse([
            'id' => $advert->getId(),
            'text' => $advert->getText(),
            'banner' => $advert->getBanner()]);
    }

    private function parseRequest(RequestInterface $request)
    {
        if (!filter_var($request->getParsedBody()['banner'], FILTER_VALIDATE_URL)) {
            throw new \LogicException('Invalid content in field "banner"', 400);
        }
        if (!is_numeric($request->getParsedBody()['price'])) {
            throw new \LogicException('Invalid content in field "price"', 400);
        }
        if (!filter_var($request->getParsedBody()['limit'], FILTER_VALIDATE_INT)) {
            throw new \LogicException('Invalid content in field "limit"', 400);
        }
        if (empty($request->getParsedBody()['text'])) {
            throw new \LogicException('Text field cannot be empty', 400);
        }
        $advert = new Advert();
        $advert->setBanner($request->getParsedBody()['banner']);
        $advert->setPrice((float)$request->getParsedBody()['price']);
        $advert->setLimit((int)$request->getParsedBody()['limit']);
        $advert->setText($request->getParsedBody()['text']);
        return $advert;
    }
}