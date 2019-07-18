<?php

namespace Camoo\Hosting\Modules;

/**
 * Class Customers
 * @author CamooSarl
 */
class Customers extends AppModules
{
    public function add($data)
    {
        return $this->client->post(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'customers/add', $data);
    }
}
