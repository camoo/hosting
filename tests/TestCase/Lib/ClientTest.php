<?php

namespace CamooHosting\Test\TestCase\Lib;

use Camoo\Hosting\Dto\AccessTokenDTO;
use Camoo\Hosting\Lib\AccessToken;
use Camoo\Hosting\Lib\Client;
use Camoo\Hosting\Lib\Response;
use Camoo\Http\Curl\Domain\Client\ClientInterface as HttpClient;
use Camoo\Http\Curl\Domain\Response\ResponseInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

/**
 * Class ClientTest
 *
 * @author CamooSarl
 *
 * @covers \Camoo\Hosting\Lib\Client
 */
class ClientTest extends TestCase
{
    private Client $client;

    private MockObject $httpMock;

    protected function setUp(): void
    {
        $accessTokenMock = $this->createMock(AccessToken::class);
        $this->httpMock = $this->createMock(HttpClient::class);

        // Mock AccessTokenDTO
        $this->accessTokenDTO = new AccessTokenDTO('valid_token', 'Bearer', 3600, time(), 'scope');

        // Setting up the AccessToken mock to return AccessTokenDTO
        $accessTokenMock->method('get')->willReturn($accessTokenMock);
        $accessTokenMock->method('getTokenDTO')->willReturn($this->accessTokenDTO);

        $this->client = new Client($accessTokenMock, null, $this->httpMock);
    }

    public function testPost()
    {
        $expectedResponse = $this->createMock(ResponseInterface::class);

        $expectedResponse->method('getStatusCode')->willReturn(200);
        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn('{"status": "OK", "message": "Success"}');
        $expectedResponse->method('getBody')->willReturn($body);

        $this->httpMock->method('sendRequest')->willReturn($expectedResponse);

        $url = 'endpoint';
        $data = ['key' => 'value'];

        // Execute the method under test
        $response = $this->client->post($url, $data);

        // Assertions to check if the response is handled correctly
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"status": "OK", "message": "Success"}', $response->getBody());

        $this->assertEquals('valid_token', $this->accessTokenDTO->accessToken);

    }

    public function testGet()
    {
        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getStatusCode')->willReturn(200);
        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn('{"status": "OK", "details": "Retrieved successfully"}');
        $expectedResponse->method('getBody')->willReturn($body);

        $this->httpMock->method('sendRequest')->willReturn($expectedResponse);

        $url = 'retrieve-data';
        $data = ['query' => 'info'];

        $response = $this->client->get($url, $data);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"status": "OK", "details": "Retrieved successfully"}', $response->getBody());
    }
}
