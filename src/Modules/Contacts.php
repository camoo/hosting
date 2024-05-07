<?php

declare(strict_types=1);

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Lib\Response;

/**
 * Class Contacts
 *
 * @author CamooSarl
 */
class Contacts extends AppModules
{
    /** @param array<string,string|int> $data */
    public function add(array $data): Response
    {
        return $this->client->post('contacts/add', $data);
    }
}
