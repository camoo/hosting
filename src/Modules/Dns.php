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
    public function activate(int $id): Response
    {
        return $this->client->post('dns/activate', ['id' => $id]);
    }

    /** @param array<string,string|int> $data */
    public function add(array $data): Response
    {
        return $this->client->post('dns/add-record', $data);
    }

    /** @param array<string,string|int> $data */
    public function delete(array $data): Response
    {
        return $this->client->post('dns/delete-record', $data);
    }
}
