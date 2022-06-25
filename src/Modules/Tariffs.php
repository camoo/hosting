<?php

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Lib\Response;

/**
 * Class Tariffs
 *
 * @author CamooSarl
 */
class Tariffs extends AppModules
{
    public function get(): Response
    {
        return $this->client->get('tariffs/get');
    }
}
