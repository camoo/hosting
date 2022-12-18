<?php

declare(strict_types=1);

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Lib\Response;

final class Orders extends AppModules
{
    public function offline(array $body): Response
    {
        return $this->client->post('order/offline', $body);
    }
}
