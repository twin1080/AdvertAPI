<?php

namespace AdvertAPI;

use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use LogicException;
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
        } catch (LogicException $e) {
            return new JsonResponse([
                "message" => $e->getMessage(),
                "code" => $e->getCode(),
                "data" => []
            ], $e->getCode() ?? 500);
        } catch (Exception $e) {
            return new TextResponse(
                $e->getMessage(),
//                "Internal Server Error",
                500);
        }
    }
}