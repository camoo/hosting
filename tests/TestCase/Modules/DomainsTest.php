<?php

namespace CamooHosting\Test\TestCase\Modules;

use Camoo\Hosting\Lib\Response;
use Camoo\Hosting\Modules\Domains;
use Camoo\Hosting\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;

/**
 * Class DomainsTest
 *
 * @author CamooSarl
 */
#[CoversClass(Domains::class)]
class DomainsTest extends TestCase
{
    #[TestWith(["test", "cm"])]
    #[TestWith(["camoo", "cm"])]
    public function testCheckAvailability(string $domain, string $tld): void
    {
        $result = $this->oClientMocked->checkAvailability($domain, $tld);
        $this->assertInstanceOf(Response::class, $result);
    }

    public function testRegister(): void
    {
        $this->assertInstanceOf(Response::class, $this->oClientMocked->register([]));
    }

    public function testRenew(): void
    {
        $this->assertInstanceOf(Response::class, $this->oClientMocked->renew([]));
    }

    public function testSuspend(): void
    {
        $this->assertInstanceOf(Response::class, $this->oClientMocked->suspend(111));
    }

    public function testUnSuspend(): void
    {
        $this->assertInstanceOf(Response::class, $this->oClientMocked->unsuspend(11));
    }

    public function testresendVerificationMail(): void
    {
        $this->assertInstanceOf(Response::class, $this->oClientMocked->resendVerificationMail(11));
    }

    public function testisTranferable(): void
    {
        $this->assertInstanceOf(Response::class, $this->oClientMocked->isTransferable('test.cm'));
    }

    public function testcmWhois(): void
    {
        $this->assertInstanceOf(Response::class, $this->oClientMocked->cmWhois('test.cm'));
    }

    public function testTransfer(): void
    {
        $this->assertInstanceOf(Response::class, $this->oClientMocked->transfer([]));
    }
}
