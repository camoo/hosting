<?php
declare(strict_types=1);

namespace Camoo\Hosting\Modules;

/**
 * Class Contacts
 * @author CamooSarl
 */
class Contacts extends AppModules
{
    public function add($data)
    {
        return $this->client->post('contacts/add', $data);
    }
}
