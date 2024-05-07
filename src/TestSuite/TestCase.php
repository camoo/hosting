<?php

namespace Camoo\Hosting\TestSuite;

use Camoo\Hosting\Lib\Client;
use Camoo\Hosting\Lib\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Class TestCase
 *
 * @author CamooSarl
 */
//@codeCoverageIgnoreStart
class TestCase extends BaseTestCase
{
    protected $oPost;
    protected $oGet;
    protected array $oResponse;
    protected MockObject $oClientMocked;

    /**
     * Sets up the environment before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Determine the class under test from the name of the test class
        $asClass = explode('\\', static::class);
        $sClass = substr(array_pop($asClass), 0, -4);  // Assuming test class names end in 'Test'
        $sClass1 = "\\Camoo\\Hosting\\Modules\\{$sClass}";

        // Mock the client being tested
        $this->oClientMocked = $this->getMockBuilder($sClass1)
            ->disableOriginalConstructor()
            ->onlyMethods(['__get'])
            ->getMock();

        // Prepare common responses for POST and GET
        $this->oPost = $this->createMock(Client::class);
        $this->oGet = $this->createMock(Client::class);

        // Setup response factory mock
        $this->oResponse = [$this, 'createResponse'];

        // Configure the mocked client to return the mocked POST/GET a client on __get
        $this->oClientMocked->method('__get')
            ->willReturnCallback(function ($name) {
                return $name === 'post' ? $this->oPost : $this->oGet;
            });

        // Mock the behavior of the POST and GET requests
        $hRes = ['result' => '{"Test": "ok"}', 'code' => 200, 'entity' => 'Domain'];
        $this->oPost->method('post')->willReturn(call_user_func($this->oResponse, $hRes));
        $this->oGet->method('get')->willReturn(call_user_func($this->oResponse, $hRes));
    }

    /**
     * Response simulation method for creating a response based on input.
     */
    protected function createResponse(array $options)
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->method('getJson')->willReturn(json_decode($options['result'], true));
        $responseMock->method('getStatusCode')->willReturn($options['code']);
        return $responseMock;
    }

    /**
     * Cleans up the environment after each test.
     */
    protected function tearDown(): void
    {
        unset($this->oClientMocked);
        parent::tearDown();
    }
}
//@codeCoverageIgnoreEnd
