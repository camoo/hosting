<?php

namespace Camoo\Hosting\Modules;

/**
 * Class Tariffs
 * @author CamooSarl
 */
class Tariffs extends AppModules
{
    public function get()
    {
        return $this->client->get(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'tariffs/get');
    }

}
