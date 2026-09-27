<?php

declare(strict_types=1);

namespace CamooHosting\Test\TestCase\Modules;

use Camoo\Hosting\Lib\Client;
use Camoo\Hosting\Lib\Response;
use Camoo\Hosting\Modules\Customers;
use Camoo\Hosting\Modules\Payments;
use PHPUnit\Framework\TestCase;

final class RequestEncodingTest extends TestCase
{
    public function testCustomerEmailIsSentAsQueryData(): void
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('get')
            ->with('customers/get-by-email', ['email' => 'customer+tag@example.com'])
            ->willReturn($this->createMock(Response::class));

        $module = new Customers($client);
        $module->getByEmail('customer+tag@example.com');
    }

    public function testPaymentIdIsSentAsQueryData(): void
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('get')
            ->with('payment/check', ['payment_id' => 'payment/with spaces'])
            ->willReturn($this->createMock(Response::class));

        $module = new Payments($client);
        $module->check('payment/with spaces');
    }
}
