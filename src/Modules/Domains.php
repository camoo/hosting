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
        return $this->client->post('domains/availability', $data);
    }

    public function register($data)
    {
        return $this->client->post('domains/register', $data);
    }

    public function renew($data)
    {
        return $this->client->post('domains/renew', $data);
    }

    public function suspend($id)
    {
        return $this->client->post('domains/suspend/'. $id);
    }

    public function unsuspend($id)
    {
        return $this->client->post('domains/unsuspend/'. $id);
    }

    public function resendVerificationMail($id)
    {
        return $this->client->post('domains/resend-verification-mail/'. $id);
    }

    public function isTranferable($domain)
    {
        return $this->client->post('domains/validate-tranfer', ['domain-name' => $domain]);
    }

    public function transfer($data)
    {
        return $this->client->post('domains/transfer', $data);
    }

    public function cmWhois($domain)
    {
        return $this->client->post('domains/cm-whois', ['domain-name' => $domain]);
    }
}
