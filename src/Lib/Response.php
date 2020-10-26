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
    private static $_status_code = 201;
    private static $_result = null;
    private static $_entity = null;

    const BAD_STATUS = 'KO';
    const GOOD_STATUS = 'OK';

    public static function create(array $option) : Response
    {
        static::$_status_code = $option['code'];
        static::$_result= $option['result'];
        if (!empty($option['entity'])) {
            static::$_entity = $option['entity'];
        }
        return new self;
    }

    public function getBody() : string
    {
        return (string) static::$_result;
    }

    public function getStatusCode() : int
    {
        return (int) static::$_status_code;
    }

    public function getJson() : array
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
            $hret = ['status' => static::BAD_STATUS, 'message' => 'request failed!'];
            return (new $class)->convert($hret);
        }
        return (new $class)->convert($this->decodeJson(static::$_result));
    }

    protected function decodeJson(string $sJSON, bool $bAsHash = false)
    {
        if (($xData = json_decode($sJSON, $bAsHash)) === null
                && (json_last_error() !== JSON_ERROR_NONE)) {
            trigger_error(json_last_error_msg(), E_USER_ERROR);
        }
        return $xData;
    }

    public function getError()
    {
        return self::$_result;
    }
}
