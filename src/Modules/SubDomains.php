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
        return $this->client->post(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'sub-domains/add', $data);
    }
}
