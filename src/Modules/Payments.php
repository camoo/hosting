<?php

declare(strict_types=1);

namespace Camoo\Hosting\Modules;

final class Payments extends AppModules
{
    public function mobileWallet(array $payload)
    {
        return $this->client->post('payment/mobile-money', $payload);
    }

    public function check(string $paymentId)
    {
        return $this->client->get('payment/check?payment_id=' . $paymentId);
    }
}
