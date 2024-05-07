<?php

declare(strict_types=1);

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Lib\Response;

/**
 * Class SubDomains
 *
 * @author CamooSarl
 */
class SubDomains extends AppModules
{
    /**
     * @param array<string,string|int> $data
     */
    public function add(array $data): Response
    {
        return $this->client->post('sub-domains/add', $data);
    }

    public function delete(int $id): Response
    {
        return $this->client->post('sub-domains/delete', ['id' => $id]);
    }
}
