<?php

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Lib\Response;

/**
 * Class Domains
 *
 * @author CamooSarl
 */
class Domains extends AppModules
{
    public function checkAvailability($domain, $tlds): Response
    {
        $data = ['domain' => $domain, 'tlds' => $tlds];

        return $this->client->post('domains/availability', $data);
    }

    public function register($data): Response
    {
        return $this->client->post('domains/register', $data);
    }

    public function renew($data): Response
    {
        return $this->client->post('domains/renew', $data);
    }

    public function suspend($id): Response
    {
        return $this->client->post('domains/suspend/' . $id);
    }

    public function unsuspend($id): Response
    {
        return $this->client->post('domains/unsuspend/' . $id);
    }

    public function resendVerificationMail($id): Response
    {
        return $this->client->post('domains/resend-verification-mail/' . $id);
    }

    public function isTranferable($domain): Response
    {
        return $this->client->post('domains/validate-tranfer', ['domain-name' => $domain]);
    }

    public function transfer($data): Response
    {
        return $this->client->post('domains/transfer', $data);
    }

    public function cmWhois($domain): Response
    {
        return $this->client->post('domains/cm-whois', ['domain-name' => $domain]);
    }
}
