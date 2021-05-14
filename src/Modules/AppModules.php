<?php
declare(strict_types=1);

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Lib\Client;
use Camoo\Hosting\Lib\AccessToken;
use RuntimeException;

/**
 * Class AppModules
 * @author CamooSarl
 */
//@codeCoverageIgnoreStart
class AppModules
{
    protected $oAccessToken = [AccessToken::class, '_get'];

    protected $entityName = null;

    private $oToken = null;

    protected function getToken() : AccessToken
    {
        $this->oToken = call_user_func($this->oAccessToken);
        return $this->oToken;
    }

    protected function getClient() : Client
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

    protected function deleteToken() : void
    {
        if (null === $this->oToken) {
            return;
        }
        $this->oToken->delete();
    }

    public function __get(string $name)
    {
        if ($name !== 'client') {
            throw new RuntimeException('BadProperty:: '. (string)$name, 404);
        }
        return $this->getClient();
    }
}
//@codeCoverageIgnoreEnd
