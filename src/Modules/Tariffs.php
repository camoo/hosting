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
        return $this->client->get('tariffs/get');
    }

}
