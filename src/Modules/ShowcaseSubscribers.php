<?php

declare(strict_types=1);

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Lib\Response;

/** Client for subscriptions collected by reseller showcase websites. */
class ShowcaseSubscribers extends AppModules
{
    public function subscribe(string $email): Response
    {
        return $this->client->post('showcase/subscribers', ['email' => $email]);
    }
}
