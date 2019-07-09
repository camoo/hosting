<?php

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Lib\Client;

/**
 * Class AppModules
 * @author CamooSarl
 */
class AppModules
{
    protected $oAccessToken = [\Camoo\Hosting\Lib\AccessToken::class, 'get'];

    private $oToken = null;

    protected function getToken()
    {
        $this->oToken = call_user_func($this->oAccessToken);
        return $this->oToken;
    }

    protected function getClient()
    {
        return new Client((string) $this->getToken());
    }

    protected function deleteToken()
    {
        if (null !== $this->oToken) {
            $this->oToken->delete();
        }
    }
}
