<?php

namespace Camoo\Hosting\Modules;

/**
 * Class Contacts
 * @author CamooSarl
 */
class Contacts extends AppModules
{
    public function add($data)
    {
        return $this->client->post(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'contacts/add', $data);
    }
}
