<?php

namespace Camoo\Hosting\TestSuite;

use Camoo\Hosting\Modules;
use Camoo\Hosting\Lib\Client;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Class TestCase
 * @author CamooSarl
 */
//@codeCoverageIgnoreStart
class TestCase extends BaseTestCase
{
    protected $oPost;
    protected $oGet;
    protected $oResponse = [\Camoo\Hosting\Lib\Response::class, 'create'];
    protected $oClientMocked;
    private $sClass = null;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp() : void
    {
        parent::setUp();
        $asClass = explode("\\", get_called_class());
        $sClass = array_pop($asClass);
        $sClass = substr($sClass, 0, -4);
        $this->sClass = "\\Camoo\\Hosting\\Modules\\{$sClass}";
        $this->oClientMocked = $this->getMockBuilder($this->sClass)
            ->setMethods(['__get'])
             //->disableOriginalConstructor()
             ->getMock();

        $this->oPost = $this->oGet = $this->getMockBuilder(Client::class)
            ->setMethods(['post', 'get'])
             ->setConstructorArgs(['some token', 'Domain'])
             ->getMock();

        $this->oClientMocked->expects($this->once())
            ->method('__get')
            ->will($this->returnValue($this->oPost));

        $hRes = ['result' => '{"Test" : "ok"}', 'code' => 200, 'entity' => 'Domain'];
        $this->oPost->expects($this->any())
            ->method('post')
            ->will($this->returnValue(call_user_func($this->oResponse, $hRes)));

        $this->oGet->expects($this->any())
            ->method('get')
            ->will($this->returnValue(call_user_func($this->oResponse, $hRes)));
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown() : void
    {
        unset($this->oClientMocked);
        parent::tearDown();
    }
}
//@codeCoverageIgnoreEnd
