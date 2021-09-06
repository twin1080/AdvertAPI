<?php

namespace AdvertAPI;

use AdvertAPI\Exception\APILogicException;
use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ResponseWrapper implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $response = $handler->handle($request);
            return $response->withPayload([
                "message" => "OK",
                "code" => 200,
                "data" => $response->getPayload()
            ]);
        } catch (APILogicException $e) {
            return new JsonResponse([
                "message" => $e->getMessage(),
                "code" => $e->getCode(),
                "data" => []
            ], $e->getCode());
        } catch (Exception $e) {
            return new TextResponse(
                "Internal Server Error",
                500);
        }
    }
}