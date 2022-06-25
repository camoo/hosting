<?php

declare(strict_types=1);

namespace Camoo\Hosting\Lib;

/**
 * Class Response
 *
 * @author CamooSarl
 */
class Response
{
    public const BAD_STATUS = 'KO';

    public const GOOD_STATUS = 'OK';

    private static int $_status_code = 201;

    private static ?string $_result = null;

    private static ?string $_entity = null;

    public static function create(array $option): Response
    {
        static::$_status_code = (int)$option['code'];
        static::$_result = $option['result'];
        if (!empty($option['entity'])) {
            static::$_entity = $option['entity'];
        }

        return new self();
    }

    public function getBody(): string
    {
        return (string)static::$_result;
    }

    public function getStatusCode(): int
    {
        return static::$_status_code;
    }

    public function getJson(): array
    {
        if ($this->getStatusCode() !== 200) {
            return ['status' => static::BAD_STATUS, 'message' => 'request failed!'];
        }

        return $this->decodeJson(static::$_result, true);
    }

    public function getEntity()
    {
        $class = '\\Camoo\\Hosting\\Entity\\' . static::$_entity;
        if ($this->getStatusCode() !== 200) {
            $entityData = ['status' => static::BAD_STATUS, 'message' => 'request failed!'];

            return (new $class())->convert($entityData);
        }

        return (new $class())->convert($this->decodeJson(static::$_result));
    }

    public function getError(): ?string
    {
        return self::$_result;
    }

    protected function decodeJson(string $sJSON, bool $bAsHash = false)
    {
        if (($xData = json_decode($sJSON, $bAsHash)) === null
                && (json_last_error() !== JSON_ERROR_NONE)) {
            trigger_error(json_last_error_msg(), E_USER_ERROR);
        }

        return $xData;
    }
}
