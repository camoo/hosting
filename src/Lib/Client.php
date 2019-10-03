<?php
namespace Camoo\Hosting\Lib;

use Exception;

class Client
{
    private $_rest = null;
    private $_code = null;
    private $_token = null;
    private $_entity = null;
    const API_ENDPOINT = 'https://api.camoo.hosting/v1/';
    protected $oResponse = [\Camoo\Hosting\Lib\Response::class, 'create'];

    public function __construct($accesstoken=null, $entity=null)
    {
        if (!$this->_isCurl()) {
            trigger_error('PHP-Curl module is missing!', E_USER_ERROR);
        }
        if (null !== $accesstoken) {
            $this->_token = $accesstoken;
        }
        if (null !== $entity) {
            $this->_entity = $entity;
        }
    }

    // @codeCoverageIgnoreStart
    protected function apiCall($url, $data=[], $type='POST')
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

    public function setToken($accesstoken=null)
    {
        if (null !== $accesstoken) {
            $this->_token = $accesstoken;
        }
    }

    // @codeCoverageIgnoreStart
    protected function getToken()
    {
        return $this->_token;
    }
    // @codeCoverageIgnoreEnd

    public function post($url, $data=[])
    {
        return call_user_func($this->oResponse, $this->apiCall($url, $data));
    }

    public function get($url, $data=[])
    {
        return call_user_func($this->oResponse, $this->apiCall($url, $data, 'get'));
    }

    protected function _isCurl()
    {
        return function_exists('curl_version');
    }
}
