<?php

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Lib\Response;

/**
 * Class Customers
 *
 * @author CamooSarl
 */
class Customers extends AppModules
{
    /**
     * @param array $data
     */
    public function add($data): Response
    {
        return $this->client->post('customers/add', $data);
    }

    /**
     * @param string $email
     */
    public function getByEmail($email): Response
    {
        return $this->client->get('customers/get-by-email/?email=' . $email);
    }

    /**
     * @param int $id
     */
    public function getById($id): Response
    {
        return $this->client->get('customers/get-by-id/?id=' . $id);
    }

    /**
     * @param array $data
     */
    public function auth($data): Response
    {
        return $this->client->post('customers/auth', $data);
    }

    /**
     * @param int $id
     */
    public function getSsoToken($id): Response
    {
        return $this->client->get('customers/sso/?id=' . $id);
    }
}
