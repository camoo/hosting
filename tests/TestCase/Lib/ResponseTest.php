<?php

declare(strict_types=1);

namespace CamooHosting\Test\TestCase\Lib;

use Camoo\Hosting\Entity\EntityInterface;
use Camoo\Hosting\Factory\EntityFactoryInterface;
use Camoo\Hosting\Lib\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use Psr\Http\Message\StreamInterface;

class ResponseTest extends TestCase
{
    private MockObject $httpResponseMock;

    private MockObject $streamMock;

    private Response $response;

    protected function setUp(): void
    {
        $this->httpResponseMock = $this->createMock(HttpResponseInterface::class);
        $this->streamMock = $this->createMock(StreamInterface::class);

        $this->httpResponseMock->method('getBody')->willReturn($this->streamMock);
        $this->factoryMock = $this->createMock(EntityFactoryInterface::class);
    }

    public function testGetStatusCode()
    {
        $this->httpResponseMock->method('getStatusCode')->willReturn(200);
        $response = new Response($this->httpResponseMock);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetBody()
    {
        $this->streamMock->method('getContents')->willReturn('{"status":"OK","data":"some data"}');
        $response = new Response($this->httpResponseMock);
        $this->assertEquals('{"status":"OK","data":"some data"}', $response->getBody());
        $this->assertEquals('{"status":"OK","data":"some data"}', $response->getError());
    }

    public function testGetJsonWithBadStatus()
    {
        $this->httpResponseMock->method('getStatusCode')->willReturn(404);
        $response = new Response($this->httpResponseMock);
        $this->assertEquals(['status' => 'KO', 'message' => 'Request failed!'], $response->getJson());
    }

    public function testGetJsonWithGoodStatus()
    {
        $this->httpResponseMock->method('getStatusCode')->willReturn(200);
        $this->streamMock->method('getContents')->willReturn('{"status":"OK","result":{"some-key":"value"}}');
        $response = new Response($this->httpResponseMock);
        $this->assertEquals(['status' => 'OK', 'result' => ['some_key' => 'value']], $response->getJson());
    }

    public function testGetEntity()
    {
        $this->httpResponseMock->method('getStatusCode')->willReturn(200);
        $this->streamMock->method('getContents')->willReturn('{"some-key":"value"}');
        $response = new Response($this->httpResponseMock, 'SomeEntity');
        $result = $response->getEntity();
        $this->assertInstanceOf(EntityInterface::class, $result);
    }
}
