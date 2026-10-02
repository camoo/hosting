<?php

namespace CamooHosting\Test\TestCase\Modules;

use Camoo\Hosting\Lib\Response;
use Camoo\Hosting\Modules\ShowcaseSubscribers;
use Camoo\Hosting\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ShowcaseSubscribers::class)]
final class ShowcaseSubscribersTest extends TestCase
{
    public function testSubscribe(): void
    {
        self::assertInstanceOf(Response::class, $this->oClientMocked->subscribe('prospect@example.com'));
    }
}
