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
        return $this->client->post('customers/add', $data);
    }

    /**
     * @param string $email
     * @return Response
     */
    public function getByEmail($email)
    {
        return $this->client->get('customers/get-by-email/?email='. $email);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function getById($id)
    {
        return $this->client->get('customers/get-by-id/?id='.$id);
    }

    /**
     * @param array $data
     * @return Response
     */
    public function auth($data)
    {
        return $this->client->post('customers/auth', $data);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function getSsoToken($id)
    {
        return $this->client->get('customers/sso/?id='.$id);
    }

}
