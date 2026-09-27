<?php

declare(strict_types=1);

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Lib\Response;

/**
 * Class Tariffs
 *
 * @author CamooSarl
 */
class Tariffs extends AppModules
{
    public function get(?int $count = null, ?int $page = null): Response
    {
        $params = [];
        if (null !== $count) {
            $params['count'] = $count;
        }
        if (null !== $page) {
            $params['page'] = $page;
        }

        return $this->client->get('tariffs/get', $params);
    }
}
