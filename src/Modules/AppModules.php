<?php

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Lib\Client;

/**
 * Class AppModules
 * @author CamooSarl
 */
class AppModules
{
    protected $oAccessToken = [\Camoo\Hosting\Lib\AccessToken::class, '_get'];

    protected $entityName = null;

    private $oToken = null;

    protected function getToken()
    {
        $this->oToken = call_user_func($this->oAccessToken);
        return $this->oToken;
    }

    protected function getClient()
    {
        if ($fullName = get_called_class()) {
            $asFullName = explode('\\', $fullName);
            $this->entityName = array_pop($asFullName);
        }
        return new Client((string) $this->getToken(), $this->entity());
    }

    protected function entity()
    {
        if (substr($this->entityName, -1) === 's') {
            return substr($this->entityName, 0, -1);
        }
    }

    protected function deleteToken()
    {
        if (null !== $this->oToken) {
            $this->oToken->delete();
        }
    }

    public function __get($name)
    {
        if ($name === 'client') {
            return $this->getClient();
        }
        throw new \Exception('BadProperty:: '. (string)$name, 404);
    }
}
