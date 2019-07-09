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
        $url = \Camoo\Hosting\Lib\Client::API_ENDPOINT.'domains/availability';
        $data = ['domain' => $domain, 'tlds' => $tlds];
        $oResponse = $this->getClient()->post($url, $data);
        if ($oResponse->getStatusCode() === 200) {
            $hResponse = $oResponse->getJson();
            return $hResponse;
        }
    }

    public function register($data)
    {
        $url = \Camoo\Hosting\Lib\Client::API_ENDPOINT.'domains/register';
        $oResponse = $this->getClient()->post($url, $data);
        if ($oResponse->getStatusCode() === 200) {
            $hResponse = $oResponse->getJson();
            return $hResponse;
        }
    }
}
