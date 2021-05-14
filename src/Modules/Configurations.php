<?php
declare(strict_types=1);

namespace Camoo\Hosting\Modules;

/**
 * Class Configurations
 * @author CamooSarl
 */
final class Configurations extends AppModules
{
    public function get()
    {
        return $this->client->get('configuration/get');
    }
}
