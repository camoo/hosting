<?php

namespace Camoo\Hosting\Modules;

/**
 * Class Customers
 * @author CamooSarl
 */
class Customers extends AppModules
{
    public function add($data)
    {
        $url = \Camoo\Hosting\Lib\Client::API_ENDPOINT.'customers/add';
        $oResponse = $this->getClient()->post($url, $data);
        if ($oResponse->getStatusCode() === 200) {
            $hResponse = $oResponse->getJson();
            return $hResponse;
        }
    }
}
