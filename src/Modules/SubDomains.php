<?php

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Lib\Response;

/**
 * Class SubDomains
 *
 * @author CamooSarl
 */
class SubDomains extends AppModules
{
    public function add($data): Response
    {
        return $this->client->post('sub-domains/add', $data);
    }

    public function delete($id): Response
    {
        return $this->client->post('sub-domains/delete/' . $id);
    }
}
