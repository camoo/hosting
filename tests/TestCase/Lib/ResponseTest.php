<?php

namespace CamooHosting\Test\TestCase\Lib;

use PHPUnit\Framework\TestCase;
use Camoo\Hosting\Lib\Response;
use PHPUnit\Framework\Error\Error;

/**
 * Class ResponseTest
 * @author CamooSarl
 * @covers \Camoo\Hosting\Lib\Response
 */
class ResponseTest extends TestCase
{

    /**
     * @covers \Camoo\Hosting\Lib\Response::create
     * @dataProvider createDataProvider
     */
    public function testCreate($option)
    {
        $response = Response::create($option);
        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @covers \Camoo\Hosting\Lib\Response::getBody
     * @dataProvider createDataProvider
     */
    public function testGetBody($option)
    {
        $response = Response::create($option);
        $this->assertEquals($response->getBody(), $option['result']);
    }

    /**
     * @covers \Camoo\Hosting\Lib\Response::getStatusCode
     * @dataProvider createDataProvider
     */
    public function testGetStatusCode($option)
    {
        $response = Response::create($option);
        $this->assertEquals($response->getStatusCode(), $option['code']);
    }

    /**
     * @covers \Camoo\Hosting\Lib\Response::getEntity
     * @dataProvider createDataProvider
     */
    public function testGetEntity($option)
    {
        $response = Response::create($option);
        $this->assertIsObject($response->getEntity());
    }

    /**
     * @covers \Camoo\Hosting\Lib\Response::getJson
     * @dataProvider createDataProvider
     */
    public function testGetJson($option)
    {
        $response = Response::create($option);
        $this->assertIsArray($response->getJson());
    }

    /**
     * @covers \Camoo\Hosting\Lib\Response::getJson
     * @dataProvider createDataProviderFailure
     */
    public function testGetJsonFailure($option)
    {
        $this->expectException(Error::class);
        $response = Response::create($option);
        $this->assertNull($response->getJson());
    }

    public function createDataProvider()
    {
        return [
            [['code' => 200, 'result' => '{"Test":"OK"}', 'entity' => 'Domain']],
            [['code' => 500, 'result' => '{"Test":"NOK"}']],
            [['code' => 404, 'result' => '{"Test":"NOK"}', null]],
        ];
    }

    public function createDataProviderFailure()
    {
        return [
            [['code' => 200, 'result' => '{"NOK"}']],
            [['code' => 200, 'result' => null, null]],
        ];
    }
}
