<?php

declare(strict_types=1);

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Lib\Response;

/**
 * Class Domains
 *
 * @author CamooSarl
 */
class Domains extends AppModules
{
    public function checkAvailability(string $domain, string $tlds): Response
    {
        $data = ['domain' => $domain, 'tlds' => $tlds];

        return $this->client->post('domains/availability', $data);
    }

    public function register(array $data): Response
    {
        return $this->client->post('domains/register', $data);
    }

    public function renew(array $data): Response
    {
        return $this->client->post('domains/renew', $data);
    }

    public function suspend(int $id): Response
    {
        return $this->client->post('domains/suspend', ['id' => $id]);
    }

    public function unsuspend(int $id): Response
    {
        return $this->client->post('domains/unsuspend', ['id' => $id]);
    }

    public function resendVerificationMail(int $id): Response
    {
        return $this->client->post('domains/resend-verification-mail', ['id' => $id]);
    }

    public function isTranferable(string $domain): Response
    {
        return $this->client->post('domains/validate-transfer', ['domain-name' => $domain]);
    }

    public function transfer(array $data): Response
    {
        return $this->client->post('domains/transfer', $data);
    }

    public function cmWhois(string $domain): Response
    {
        return $this->client->post('domains/cm-whois', ['domain-name' => $domain]);
    }
}
