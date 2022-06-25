<?php

declare(strict_types=1);

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Exception\ModuleException;
use Camoo\Hosting\Lib\AccessToken;
use Camoo\Hosting\Lib\Client;

/**
 * Class AppModules
 *
 * @property Client $client
 *
 * @author CamooSarl
 */
//@codeCoverageIgnoreStart
class AppModules
{
    protected array $oAccessToken = [AccessToken::class, '_get'];

    protected ?string $entityName = null;

    private ?AccessToken $oToken = null;

    public function __get(string $name)
    {
        if ($name !== 'client') {
            throw new ModuleException('BadProperty:: ' . $name, 404);
        }

        return $this->getClient();
    }

    protected function getToken(): AccessToken
    {
        $this->oToken = call_user_func($this->oAccessToken);

        return $this->oToken;
    }

    protected function getClient(): Client
    {
        $fullName = get_called_class();
        if (empty($fullName)) {
            throw new ModuleException('Called class not found !');
        }

        $asFullName = explode('\\', $fullName);
        $this->entityName = array_pop($asFullName);

        return new Client((string)$this->getToken(), $this->entity());
    }

    protected function entity(): ?string
    {
        if (null === $this->entityName) {
            return null;
        }

        if (substr($this->entityName, -1) !== 's') {
            return null;
        }

        return substr($this->entityName, 0, -1);
    }

    protected function deleteToken(): void
    {
        if (null === $this->oToken) {
            return;
        }
        $this->oToken->delete();
    }
}
//@codeCoverageIgnoreEnd
