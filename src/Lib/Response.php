<?php

declare(strict_types=1);

namespace Camoo\Hosting\Lib;

use Camoo\Hosting\Entity\EntityInterface;
use Camoo\Hosting\Factory\EntityFactory;
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

    /**
     * @param array<string,mixed> $option
     */
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

    /** @return array<string, mixed> */
    public function getJson(): array
    {
        if ($this->getStatusCode() !== 200) {
            return ['status' => static::BAD_STATUS, 'message' => 'Request failed!'];
        }

        $response = $this->decodeJson($this->getBody(), true);

        if (!is_array($response)) {
            return ['status' => static::BAD_STATUS, 'message' => 'Invalid JSON response'];
        }

        if (isset($response['result']) && is_array($response['result'])) {
            $response['result'] = $this->normalizeKeys($response['result']);
        }

        return $response;
    }

    public function getEntity(): ?EntityInterface
    {
        if (null === $this->entity) {
            return null;
        }
        $class = EntityFactory::create()->getEntityClass($this->entity);
        if ($this->getStatusCode() !== 200) {
            $entityData = ['status' => static::BAD_STATUS, 'message' => 'request failed!'];

            return $class->convert($entityData);
        }

        return $class->convert($this->decodeJson($this->getBody()));
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

    /**
     * Normalize keys in an array to replace hyphens with underscores.
     *
     * @param array<string,mixed> $array The array with keys to normalize.
     *
     * @return array<string,mixed> The array with normalized keys.
     */
    private function normalizeKeys(array $array): array
    {
        $normalizedArray = [];
        foreach ($array as $key => $value) {
            $normalizedKey = str_replace('-', '_', $key);
            $normalizedArray[$normalizedKey] = $value;
        }

        return $normalizedArray;
    }
}
