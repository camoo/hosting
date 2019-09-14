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

    public function suspend($id)
    {
        return $this->client->post(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'domains/suspend/'. $id);
    }

    public function unsuspend($id)
    {
        return $this->client->post(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'domains/unsuspend/'. $id);
    }

    public function resendVerificationMail($id)
    {
        return $this->client->post(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'domains/resend-verification-mail/'. $id);
    }

    public function isTranferable($domain)
    {
        return $this->client->post(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'domains/validate-tranfer', ['domain-name' => $domain]);
    }

    public function transfer($data)
    {
        return $this->client->post(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'domains/transfer', $data);
    }

    public function cmWhois($domain)
    {
        return $this->client->post(\Camoo\Hosting\Lib\Client::API_ENDPOINT.'domains/cm-whois', ['domain-name' => $domain]);
    }
}
