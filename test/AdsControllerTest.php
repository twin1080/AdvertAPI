<?php

use AdvertAPI\ResponseWrapper;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Middlewares\Utils\Dispatcher;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class AdsControllerTest extends TestCase
{

    private Dispatcher $dispatcher;

    public static function setUpBeforeClass(): void
    {
        if (getenv('APP_ENV') != 'autotest') {
            throw new Exception('Do not run test without phpunit.xml configuration');
        }
        parent::setUpBeforeClass();
        self::clearDatabase();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::clearDatabase();
    }

    protected static function clearDatabase(): void
    {
        $container = require __DIR__ . '/../config/container.php';
        /** @var MongoDB\Client $mongoClient */
        $mongoClient = $container->get(MongoDB\Client::class);
        $mongoClient->selectDatabase(getenv('ADS_MONGO_DATABASE'))->ads->drop([]);
    }


    protected function setUp(): void
    {
        parent::setUp();
        $container = require __DIR__ . '/../config/container.php';
        $routes = require __DIR__ . '/../config/routes.php';

        $middleware = [
            new ResponseWrapper(),
            new FastRoute($routes),
            new RequestHandler($container)
        ];

        $this->dispatcher = new Dispatcher($middleware);
    }

    public function handleDataProvider()
    {
        return [
            [
                'http://test/ads', 'POST',
                [
                    'banner' => 'https://test.ru',
                    'text' => '',
                    'limit' => '200',
                    'price' => '5.3'],
                400,
                [
                    'message' => 'Text field cannot be empty',
                    'code' => 400,
                    'data' => []
                ]
            ],
            ['http://test/ads', 'POST',
                [
                    'banner' => 'https',
                    'text' => 'text',
                    'limit' => '200',
                    'price' => '5.3'],
                400,
                [
                    'message' => 'Invalid content in field "banner"',
                    'code' => 400,
                    'data' => []
                ]],
            ['http://test/ads', 'POST',
                [
                    'banner' => 'https://test.ru',
                    'text' => 'text',
                    'limit' => 'INVALID',
                    'price' => '5.3'],
                400,
                [
                    'message' => 'Invalid content in field "limit"',
                    'code' => 400,
                    'data' => []
                ]],
            ['http://test/ads', 'POST',
                [
                    'banner' => 'https://test.ru',
                    'text' => 'text',
                    'limit' => '200',
                    'price' => 'INVALID'],
                400,
                [
                    'message' => 'Invalid content in field "price"',
                    'code' => 400,
                    'data' => []
                ]],
            ['http://test/ads/0', 'POST',
                [
                    'banner' => 'https://test.ru',
                    'text' => 'text',
                    'limit' => '200',
                    'price' => '5.3'],
                400,
                [
                    'message' => 'Not found object with id = 0',
                    'code' => 400,
                    'data' => []
                ]],
            ['http://test/ads/relevant', 'GET',
                [
                    'banner' => 'https://test.ru',
                    'text' => 'text',
                    'limit' => '200',
                    'price' => '5.3'],
                400,
                [
                    'message' => 'There are nothing to show',
                    'code' => 400,
                    'data' => []
                ]],
            ['http://test/ads', 'POST',
                [
                    'banner' => 'https://test.ru',
                    'text' => 'test',
                    'limit' => '3',
                    'price' => '5.3'],
                200,
                [
                    'message' => 'OK',
                    'code' => 200,
                    'data' => [
                        'id' => '1',
                        'text' => 'test',
                        'banner' => 'https://test.ru']
                ]],
            ['http://test/ads', 'POST',
                [
                    'banner' => 'https://test.ru',
                    'text' => 'test',
                    'limit' => '3',
                    'price' => '5.3'],
                200,
                [
                    'message' => 'OK',
                    'code' => 200,
                    'data' => [
                        'id' => '2',
                        'text' => 'test',
                        'banner' => 'https://test.ru']
                ]],
            ['http://test/ads/1', 'POST',
                [
                    'banner' => 'https://test.ru',
                    'text' => 'testEdited',
                    'limit' => '3',
                    'price' => '5.5'],
                200,
                [
                    'message' => 'OK',
                    'code' => 200,
                    'data' => [
                        'id' => '1',
                        'text' => 'testEdited',
                        'banner' => 'https://test.ru']
                ]],
            ['http://test/ads/relevant', 'GET',
                [
                    'banner' => 'https://test.ru',
                    'text' => 'testEdited',
                    'limit' => '3',
                    'price' => '5.5'],
                200,
                [
                    'message' => 'OK',
                    'code' => 200,
                    'data' => [
                        'id' => '1',
                        'text' => 'testEdited',
                        'banner' => 'https://test.ru']
                ]],
            ['http://test/ads/relevant', 'GET',
                [],
                200,
                [
                    'message' => 'OK',
                    'code' => 200,
                    'data' => [
                        'id' => '2',
                        'text' => 'test',
                        'banner' => 'https://test.ru']
                ]],
            ['http://test/wrongaddress', 'POST',
                [
                    'banner' => 'https://test.ru',
                    'text' => 'testEdited',
                    'limit' => '3',
                    'price' => '5.5'],
                404,
                []],
            ['http://test/ads/relevant', 'POST',
                [
                    'banner' => 'https://test.ru',
                    'text' => 'testEdited',
                    'limit' => '3',
                    'price' => '5.5'],
                405,
                []],

        ];
    }

    /**
     * @dataProvider handleDataProvider
     */
    public function testHandle($url, $method, $requestBody, $responseStatus, $responseBody)
    {
        $request = (new ServerRequest([], [], $url, $method))->withParsedBody($requestBody);
        $response = $this->dispatcher->handle($request);

        $this->assertResponse($response, $responseBody, $responseStatus);
    }

    /**
     * @param ResponseInterface $response
     * @param $expectedResponse
     * @param $expectedStatus
     */
    protected function assertResponse(
        ResponseInterface $response,
                          $expectedResponse,
                          $expectedStatus): void
    {
        if ($response instanceof JsonResponse) {
            $this->assertEquals(
                $expectedResponse, $response->getPayload());
            $this->assertEquals($expectedStatus, $response->getStatusCode());
        } else {
            $this->assertEquals($expectedStatus, $response->getStatusCode());
        }
    }
}
