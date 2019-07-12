<?php

namespace Camoo\Hosting\Lib;
use Client;

/**
 * Class Response
 * @author CamooSarl
 */
class Response
{
    private static $_status_code = 201;
    private static $_result = null;
	const BAD_STATUS = 'KO';
	const GOOD_STATUS = 'OK';

    public static function create($option)
    {
        static::$_status_code=$option['code'];
        static::$_result=$option['result'];
        return new self;
    }

    public function getBody()
    {
        return (string) static::$_result;
    }

    public function getStatusCode()
    {
        return (int) static::$_status_code;
    }

    public function getJson()
    {
        return $this->decodeJson(static::$_result, true);
    }

    protected function decodeJson($sJSON, $bAsHash = false)
    {
        try {
            if (($xData = json_decode($sJSON, $bAsHash)) === null
                && (json_last_error() !== JSON_ERROR_NONE)) {
                return null;
                trigger_error(json_last_error_msg(), E_USER_ERROR);
            }
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            return null;
        }
        return $xData;
    }
}
