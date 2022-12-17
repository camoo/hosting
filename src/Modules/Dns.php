<?php

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Lib\Response;

/**
 * Class Dns
 *
 * @author CamooSarl
 */
class Dns extends AppModules
{
    public function activate($id): Response
    {
        return $this->client->post('dns/activate', ['id' => $id]);
    }

    public function add($data): Response
    {
        return $this->client->post('dns/add-record', $data);
    }

    public function delete($data): Response
    {
        return $this->client->post('dns/delete-record', $data);
    }
}
