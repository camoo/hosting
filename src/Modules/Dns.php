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
        return $this->client->post('dns/activate/'. $id);
    }

    public function add($data)
    {
        return $this->client->post('dns/add-record', $data);
    }

    public function delete($data)
    {
        return $this->client->post('dns/delete-record', $data);
    }
}
