<?php

namespace Camoo\Hosting\Modules;

/**
 * Class Dns
 * @author CamooSarl
 */
class Dns extends AppModules
{
    public function activate($id)
    {
        return $this->client->post(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'dns/activate/'. $id);
    }

    public function add($data)
    {
        return $this->client->post(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'dns/add-record', $data);
    }

    public function delete($data)
    {
        return $this->client->post(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'dns/delete-record', $data);
    }
}
