<?php

declare(strict_types=1);

namespace Camoo\Hosting\Modules;

final class Payments extends AppModules
{
    /**
     * @param array<string,string|int|float> $payload
     */
    public function mobileWallet(array $payload): \Camoo\Hosting\Lib\Response
    {
        return $this->client->post('payment/mobile-money', $payload);
    }

    public function check(string $paymentId): \Camoo\Hosting\Lib\Response
    {
        return $this->client->get('payment/check?payment_id=' . $paymentId);
    }
}
