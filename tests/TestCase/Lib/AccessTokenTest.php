<?php

namespace CamooHosting\Test\TestCase\Lib;

use Camoo\Hosting\Dto\AccessTokenDTO;
use Camoo\Hosting\Lib\AccessToken;
use Camoo\Hosting\Lib\Client;
use Camoo\Hosting\Lib\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class AccessTokenTest extends TestCase
{
    private AccessToken $accessToken;

    private Client $client;

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->accessToken = new AccessToken($this->client);
        $this->accessToken->delete(); // Ensure a clean state before each test
    }

    public function testGetInstance()
    {
        $this->assertInstanceOf(AccessToken::class, $this->accessToken);
    }

    public function testDeleteToken()
    {
        $this->accessToken->delete();
        $this->assertNull($this->accessToken->getTokenDTO());
    }

    public function testGetWithFreshToken()
    {
        // Prepare the mock response object
        $response = $this->createMock(ResponseInterface::class);
        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn(json_encode([
            'status' => 'OK',
            'result' => [
                'access_token' => 'abc123',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
                'scope' => 'test_scope',
            ],
        ]));

        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($body);

        // Setting up the client mock to return the response
        $this->client->method('post')->willReturn(new Response($response));

        // Test getting a new token
        $result = $this->accessToken->get(['email' => 'user@example.com', 'password' => 'secret']);
        $tokenDTO = $result->getTokenDTO();

        $this->assertInstanceOf(AccessTokenDTO::class, $tokenDTO);
        $this->assertEquals('abc123', $tokenDTO->accessToken);
        $this->assertEquals('Bearer', $tokenDTO->tokenType);
        $this->assertEquals('test_scope', $tokenDTO->scope);
    }
}
