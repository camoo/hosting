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
    public function add($data): Response
    {
        return $this->client->post('contacts/add', $data);
    }
}
