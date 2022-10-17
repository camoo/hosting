<?php

declare(strict_types=1);

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Lib\Response;

/**
 * Class Customers
 *
 * @author CamooSarl
 */
class Customers extends AppModules
{
    public function add(array $data): Response
    {
        return $this->client->post('customers/add', $data);
    }

    public function getByEmail(string $email): Response
    {
        return $this->client->get('customers/get-by-email/?email=' . $email);
    }

    public function getById(int $id): Response
    {
        return $this->client->get('customers/get-by-id/?id=' . $id);
    }

    public function auth(array $data): Response
    {
        return $this->client->post('customers/auth', $data);
    }

    public function getSsoToken(int $id): Response
    {
        return $this->client->get('customers/sso/?id=' . $id);
    }
}
