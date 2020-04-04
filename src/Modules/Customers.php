<?php

namespace Camoo\Hosting\Modules;

/**
 * Class Customers
 * @author CamooSarl
 */
class Customers extends AppModules
{
    /**
     * @param array $data
     * @return Response
     */
    public function add($data)
    {
        return $this->client->post(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'customers/add', $data);
    }

    /**
     * @param string $email
     * @return Response
     */
    public function getByEmail($email)
    {
        return $this->client->get(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'customers/get-by-email/?email='. $email);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function getById($id)
    {
        return $this->client->get(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'customers/get-by-id/?id='.$id);
    }

    /**
     * @param array $data
     * @return Response
     */
    public function auth($data)
    {
        return $this->client->post(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'customers/auth', $data);
    }
}
