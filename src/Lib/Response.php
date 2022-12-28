<?php

declare(strict_types=1);

namespace Camoo\Hosting\Lib;

use Psr\Http\Message\ResponseInterface;

/**
 * Class Response
 *
 * @author CamooSarl
 */
class Response
{
    public const BAD_STATUS = 'KO';

    public const GOOD_STATUS = 'OK';

    public function __construct(private ResponseInterface $response, private ?string $entity = null)
    {
    }

    public static function create(array $option): self
    {
        return new self($option['response'], $option['entity'] ?? null);
    }

    public function getBody(): string
    {
        return (string)$this->response->getBody();
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function getJson(): array
    {
        if ($this->getStatusCode() !== 200) {
            return ['status' => static::BAD_STATUS, 'message' => 'request failed!'];
        }

        return $this->decodeJson($this->getBody(), true);
    }

    public function getEntity(): mixed
    {
        if (null === $this->entity) {
            return null;
        }
        $class = '\\Camoo\\Hosting\\Entity\\' . $this->entity;
        if ($this->getStatusCode() !== 200) {
            $entityData = ['status' => static::BAD_STATUS, 'message' => 'request failed!'];

            return (new $class())->convert($entityData);
        }

        return (new $class())->convert($this->decodeJson($this->getBody()));
    }

    public function getError(): ?string
    {
        return $this->getBody();
    }

    protected function decodeJson(string $sJSON, bool $bAsHash = false): mixed
    {
        if (($xData = json_decode($sJSON, $bAsHash)) === null
                && (json_last_error() !== JSON_ERROR_NONE)) {
            trigger_error(json_last_error_msg(), E_USER_ERROR);
        }

        return $xData;
    }
}
