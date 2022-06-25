<?php

declare(strict_types=1);

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Lib\Response;

/**
 * Class Configurations
 *
 * @author CamooSarl
 */
final class Configurations extends AppModules
{
    public function get(): Response
    {
        return $this->client->get('configuration/get');
    }
}
