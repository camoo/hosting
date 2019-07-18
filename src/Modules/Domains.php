<?php

namespace Camoo\Hosting\Modules;

/**
 * Class Domains
 * @author CamooSarl
 */
class Domains extends AppModules
{
    public function checkAvailability($domain, $tlds)
    {
        $data = ['domain' => $domain, 'tlds' => $tlds];
        return $this->client->post(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'domains/availability', $data);
    }

    public function register($data)
    {
        return $this->client->post(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'domains/register', $data);
    }

    public function renew($data)
    {
        return $this->client->post(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'domains/renew', $data);
    }
}
