<?php

namespace Camoo\Hosting\Modules;

/**
 * Class Contacts
 * @author yourname
 */
class Contacts extends AppModules
{
    public function add($data)
    {
        $url = \Camoo\Hosting\Lib\Client::API_ENDPOINT.'contacts/add';
        $oResponse = $this->getClient()->post($url, $data);
        if ($oResponse->getStatusCode() === 200) {
            $hResponse = $oResponse->getJson();
            return $hResponse;
        }
    }
}
