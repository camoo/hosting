<?php

namespace CamooHosting\Test\TestCase\Modules;

use Camoo\Hosting\Lib\Response;
use Camoo\Hosting\TestSuite\TestCase;

/**
 * Class DomainsTest
 *
 * @author CamooSarl
 *
 * @covers \Camoo\Hosting\Modules\Domains
 */
class DomainsTest extends TestCase
{
    /**
     * @covers \Camoo\Hosting\Modules\Domains::checkAvailability
     *
     * @testWith        ["test", "cm"]
     * 					["camoo", "cm"]
     */
    public function testCheckAvailability($domain, $tld)
    {
        $result = $this->oClientMocked->checkAvailability($domain, $tld);
        $this->assertInstanceOf(Response::class, $result);
    }

    /** @covers \Camoo\Hosting\Modules\Domains::register */
    public function testRegister()
    {
        $this->assertInstanceOf(Response::class, $this->oClientMocked->register([]));
    }

    /** @covers \Camoo\Hosting\Modules\Domains::renew */
    public function testRenew()
    {
        $this->assertInstanceOf(Response::class, $this->oClientMocked->renew([]));
    }

    /** @covers \Camoo\Hosting\Modules\Domains::suspend */
    public function testSuspend()
    {
        $this->assertInstanceOf(Response::class, $this->oClientMocked->suspend(111));
    }

    /** @covers \Camoo\Hosting\Modules\Domains::unsuspend */
    public function testUnSuspend()
    {
        $this->assertInstanceOf(Response::class, $this->oClientMocked->unsuspend(11));
    }

    /** @covers \Camoo\Hosting\Modules\Domains::resendVerificationMail */
    public function testresendVerificationMail()
    {
        $this->assertInstanceOf(Response::class, $this->oClientMocked->resendVerificationMail(11));
    }

    /** @covers \Camoo\Hosting\Modules\Domains::isTransferable */
    public function testisTranferable()
    {
        $this->assertInstanceOf(Response::class, $this->oClientMocked->isTranferable('test.cm'));
    }

    /** @covers \Camoo\Hosting\Modules\Domains::cmWhois */
    public function testcmWhois()
    {
        $this->assertInstanceOf(Response::class, $this->oClientMocked->cmWhois('test.cm'));
    }

    /** @covers \Camoo\Hosting\Modules\Domains::transfer */
    public function testTransfer()
    {
        $this->assertInstanceOf(Response::class, $this->oClientMocked->transfer([]));
    }
}
