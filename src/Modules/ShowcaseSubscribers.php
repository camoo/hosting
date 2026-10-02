<?php

declare(strict_types=1);

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Lib\Response;

/** Client for subscriptions collected by reseller showcase websites. */
class ShowcaseSubscribers extends AppModules
{
    public function subscribe(string $email, ?string $website = null): Response
    {
        $data = ['email' => $email];
        if ($website !== null && trim($website) !== '') {
            $data['website'] = trim($website);
        }

        return $this->client->post('showcase/subscribers', $data);
    }
}
