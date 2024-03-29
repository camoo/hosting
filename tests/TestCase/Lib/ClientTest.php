<?php

namespace CamooHosting\Test\TestCase\Lib;

use Camoo\Hosting\Lib\Client;
use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\TestCase;

/**
 * Class ClientTest
 *
 * @author CamooSarl
 *
 * @covers \Camoo\Hosting\Lib\Client
 */
class ClientTest extends TestCase
{
    private $oClientMocked;

    /** setUp method */
    public function setUp(): void
    {
        parent::setUp();
        $this->oClientMocked = $this->createMock(Client::class);
    }

    /** tearDown method */
    public function tearDown(): void
    {
        unset($this->oClientMocked);
        parent::tearDown();
    }

    /**
     * @dataProvider constructorSuccess
     *
     * @param mixed|null $accesstoken
     * @param mixed|null $entity
     */
    public function testInstance($accesstoken = null, $entity = null)
    {
        $this->oClientMocked = $this->getMockBuilder(Client::class)
            ->setConstructorArgs([$accesstoken, $entity])
            ->getMock();
        $this->assertInstanceOf(Client::class, $this->oClientMocked);

        return $this->oClientMocked;
    }

    /**
     * @dataProvider constructorSuccess
     *
     * @param mixed|null $accesstoken
     * @param mixed|null $entity
     */
    public function testInstanceFailure($accesstoken = null, $entity = null)
    {
        $this->expectException(Error::class);
        $this->oClientMocked = $this->getMockBuilder(Client::class)
            ->setMethods(['_isCurl'])
            ->setConstructorArgs([$accesstoken, $entity])
            ->getMock();
    }

    /**
     * @covers \Camoo\Hosting\Lib\Client::setToken
     *
     * @dataProvider setTokenProvider
     */
    public function testSetToken($token)
    {
        $client = new Client();
        $this->assertNull($client->setToken($token));
    }

    /**
     * @covers \Camoo\Hosting\Lib\Client::post
     *
     * @dataProvider postDataProvider
     */
    public function testPost($url, $data = [])
    {
        $this->oClientMock = $this->getMockBuilder(Client::class)
            ->setMethods(['apiCall'])
            ->setConstructorArgs(['kdhkjdhkjdhkdh'])
            ->getMock();

        $this->oClientMock->expects($this->once())
            ->method('apiCall')
            ->will($this->returnValue(['result' => '{"test":"OK"}', 'code' => 200, 'entity' => null]));
        $this->assertNotNull($this->oClientMock->post($url, $data));
    }

    /**
     * @covers \Camoo\Hosting\Lib\Client::get
     *
     * @dataProvider postDataProvider
     */
    public function testGet($url, $data = [])
    {
        $this->oClientMock = $this->getMockBuilder(Client::class)
            ->setMethods(['apiCall'])
            ->setConstructorArgs(['kdhkjdhkjdhkdh'])
            ->getMock();

        $this->oClientMock->expects($this->once())
            ->method('apiCall')
            ->will($this->returnValue(['result' => '{"test":"OK"}', 'code' => 200, 'entity' => null]));
        $this->assertNotNull($this->oClientMock->get($url, $data));
    }

    public function constructorSuccess()
    {
        return [
            [],
            ['ffhjgkfghjfgkfghkfuzhfk', null],
            ['ffhjgkfghjfgkfghkfuzhfkdd', 'Domain'],
        ];
    }

    public function setTokenProvider()
    {
        return [
            [null],
            ['hfkjgheiuzie76e7i6eieutieuteiut'],
            ['hfkjghegigiiuzie76e7i6eieutieuteiut'],
            ['hfkjghghgheiuzie76e7i6eieutieuteiut'],
        ];
    }

    public function postDataProvider()
    {
        return [
            ['https://api.google.com', []],
            ['https://api.camoo.com', ['domain' => 'camoo.cm']],
            ['https://api.yahoo.com', ['domain' => 'camoo', 'tld' => 'cm']],
        ];
    }
}
