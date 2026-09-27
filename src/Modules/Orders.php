<?php

declare(strict_types=1);

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Lib\Response;

final class Orders extends AppModules
{
    /**
     * @param array<string, mixed> $body
     */
    public function offline(array $body): Response
    {
        $payload = array_key_exists('body', $body) ? $body : ['body' => json_encode($body)];

        return $this->client->post('order/offline', $payload);
    }

    /**
     * @param array<string, mixed> $body
     */
    public function online(array $body): Response
    {
        $payload = array_key_exists('body', $body) ? $body : ['body' => json_encode($body)];

        return $this->client->post('order/online', $payload);
    }
}
