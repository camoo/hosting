<?php
declare(strict_types=1);

namespace Camoo\Hosting\Lib;

use Camoo\Hosting\Lib\Response;
use Camoo\Hosting\Exception\ClientException;

class Client
{
    private $_rest = null;
    private $_code = null;
    private $_token = null;
    private $_entity = null;

    public const API_ENDPOINT = 'https://api.camoo.hosting/v1/';

    protected $oResponse = [Response::class, 'create'];

    public function __construct(?string $accesstoken=null, ?string $entity=null)
    {
        if (!$this->_isCurl()) {
            throw new ClientException('PHP-Curl module is missing!', E_USER_ERROR);
        }
        if (null !== $accesstoken) {
            $this->_token = $accesstoken;
        }
        if (null !== $entity) {
            $this->_entity = $entity;
        }
    }

    // @codeCoverageIgnoreStart
    protected function apiCall(string $url, array $data=[], string $type='POST') : array
    {
        $crl = curl_init($url);
        $headr = [];
        $headr[] = 'Content-type: application/json';
        if (null !== $this->getToken()) {
            $headr[] = 'Authorization: Bearer '.$this->getToken();
        }
        curl_setopt($crl, CURLOPT_CUSTOMREQUEST, strtoupper($type));

        curl_setopt($crl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($crl, CURLOPT_HTTPHEADER, $headr);

        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_TIMEOUT, 30);
        $rest = curl_exec($crl);
        $this->_code = curl_getinfo($crl, CURLINFO_HTTP_CODE);
        curl_close($crl);
        $this->_rest = $rest;
        return ['result' => $this->_rest, 'code' => $this->_code, 'entity' => $this->_entity];
    }
    // @codeCoverageIgnoreEnd

    public function setToken(?string $accesstoken=null) : void
    {
        if (null !== $accesstoken) {
            $this->_token = $accesstoken;
        }
    }

    // @codeCoverageIgnoreStart
    protected function getToken() : ?string
    {
        return $this->_token;
    }
    // @codeCoverageIgnoreEnd

    public function post(string $url, array $data=[]) : Response
    {
        return call_user_func($this->oResponse, $this->apiCall($url, $data));
    }

    public function get(string $url, array $data=[]) : Response
    {
        return call_user_func($this->oResponse, $this->apiCall($url, $data, 'get'));
    }

    protected function _isCurl() : bool
    {
        return function_exists('curl_version');
    }
}
