<?php

namespace CamooHosting\Test\TestCase\Lib;

use Camoo\Hosting\Lib\AccessToken;
use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\TestCase;

define('ACCESS_TOKEN_SALT', 'fjfhfjfhfjfh');
define('cm_email', 'you@gmail.com');
define('cm_passwd', '2BSe3@pMRbCnV>J(G');

/**
 * Class AccessTokenTest
 *
 * @author CamooSarl
 *
 * @covers \Camoo\Hosting\Lib\AccessToken
 */
class AccessTokenTest extends TestCase
{
    private $oAccessTokenMocked;

    public function setUp(): void
    {
        $this->oAccessTokenMocked = $this->getMockBuilder(AccessToken::class)
            ->setMethods(['apiCall'])
            ->getMock();

        $this->oAccessTokenMocked->expects($this->any())
            ->method('apiCall')
            ->will($this->returnValue(['result' => ['access_token' => time() . 'khddkjdhdjdhoid847d_f'], 'code' => 200, 'entity' => null]));
    }

    /**
     * @covers \Camoo\Hosting\Lib\AccessToken::get
     *
     * @dataProvider getDataProvider
     */
    public function testGetNonCached($option)
    {
        if (!empty($option)) {
            $asEmail = explode('@', $option['email']);
            $sTmpName = $asEmail[0];
            $_cacheFile = dirname(dirname(dirname(__DIR__))) . '/tmp/' . $sTmpName . '.cm';
            if (file_exists($_cacheFile)) {
                unlink($_cacheFile);
            }
        }
        $oClientMock = $this->getMockBuilder(AccessToken::class)
            ->setMethods(['apiCall'])
            ->getMock();

        $oClientMock->expects($this->any())
            ->method('apiCall')
            ->will($this->returnValue(['result' => ['access_token' => time() . 'khddkjdhdjdhoid847d_f'], 'code' => 200, 'entity' => null]));

        $get = $oClientMock->get($option);
        $this->assertInstanceOf(AccessToken::class, $get);
        $this->assertInstanceOf(AccessToken::class, $oClientMock::_get($option));
        $this->assertNotEmpty((string)$get);
    }

    /**
     * @covers \Camoo\Hosting\Lib\AccessToken::get
     *
     * @dataProvider getDataProvider
     */
    public function testGetException($option)
    {
        $this->expectException(Error::class);
        $oClientMock = $this->getMockBuilder(AccessToken::class)
            ->setMethods(['apiCall'])
            ->getMock();

        $oClientMock->expects($this->any())
            ->method('apiCall')
            ->will($this->returnValue(['result' => ['access_token' => time() . 'khddkjdhdjdhoid847d_f'], 'code' => 200, 'entity' => null]));

        $oClientMock::token($option);
    }

    /**
     * @covers \Camoo\Hosting\Lib\AccessToken::get
     *
     * @dataProvider getDataProvider
     */
    public function testGetCached($option)
    {
        $get = $this->oAccessTokenMocked->get($option);
        $this->assertIsObject($get);
    }

    /**
     * @covers \Camoo\Hosting\Lib\AccessToken::get
     *
     * @dataProvider getDataProvider
     */
    public function testGetCachedExpired($option)
    {
        if (!empty($option)) {
            $asEmail = explode('@', $option['email']);
            $sTmpName = $asEmail[0];
            $_cacheFile = dirname(dirname(dirname(__DIR__))) . '/tmp/' . $sTmpName . '.cm';
            if (file_exists($_cacheFile)) {
                touch($_cacheFile, time() - 1800);
            }
        }

        $get = $this->oAccessTokenMocked->get($option);
        $this->assertIsObject($get);
    }

    /**
     * @covers \Camoo\Hosting\Lib\AccessToken::delete
     *
     * @dataProvider getDataProvider
     */
    public function testDelete($option)
    {
        $get = $this->oAccessTokenMocked->get($option);
        $this->assertNull($get->delete());
    }

    public function getDataProvider()
    {
        return [
            [['email' => 'test@gmail.com', 'password' => 'TopSecret!']],
            [[]],
        ];
    }
}
