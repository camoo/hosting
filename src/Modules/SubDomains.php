<?php

namespace Camoo\Hosting\Modules;

/**
 * Class SubDomains
 * @author CamooSarl
 */
class SubDomains extends AppModules
{
    public function add($data)
    {
        return $this->client->post('sub-domains/add', $data);
    }

    public function delete($id)
    {
        return $this->client->post('sub-domains/delete/'. $id);
    }
}
