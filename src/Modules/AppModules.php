<?php

declare(strict_types=1);

namespace Camoo\Hosting\Modules;

use Camoo\Hosting\Exception\ModuleException;
use Camoo\Hosting\Lib\AccessToken;
use Camoo\Hosting\Lib\Client;

/**
 * Class AppModules
 *
 * @property-read  Client $client
 *
 * @author CamooSarl
 */
//@codeCoverageIgnoreStart
class AppModules
{
    protected ?string $entityName = null;

    /**
     * Magic method for property access. Supports 'client' and 'accessToken' properties for lazy loading.
     *
     * @param string $name Property name.
     *
     * @throws ModuleException Thrown when attempting to access a property that does not exist or is not allowed.
     */
    public function __get(string $name): Client
    {

        return match ($name) {
            'client' => $this->getClient(),
            default => throw new ModuleException('Invalid property access: ' . $name, 404),
        };
    }

    protected function getClient(): Client
    {
        $fullName = get_called_class();
        if (empty($fullName)) {
            throw new ModuleException('Called class not found !');
        }

        $asFullName = explode('\\', $fullName);
        $this->entityName = array_pop($asFullName);

        return new Client(AccessToken::getInstance()->get(), $this->getEntityName());
    }

    protected function getEntityName(): ?string
    {
        if (null === $this->entityName) {
            return null;
        }

        if (!str_ends_with($this->entityName, 's')) {
            return null;
        }

        return substr($this->entityName, 0, -1);
    }
}
//@codeCoverageIgnoreEnd
