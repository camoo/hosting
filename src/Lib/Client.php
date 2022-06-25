<?php

declare(strict_types=1);

namespace Camoo\Hosting\Lib;

use Camoo\Hosting\Exception\ClientException;

class Client
{
    private const API_ENDPOINT = 'https://api.camoo.hosting/v1/';

    protected array $oResponse = [Response::class, 'create'];

    private ?string $_token = null;

    private ?string $_entity = null;

    public function __construct(?string $accessToken = null, ?string $entity = null)
    {
        if (!$this->_isCurl()) {
            throw new ClientException('PHP-Curl module is missing!', E_USER_ERROR);
        }

        if (null !== $accessToken) {
            $this->_token = $accessToken;
        }
        if (null !== $entity) {
            $this->_entity = $entity;
        }
    }
    // @codeCoverageIgnoreEnd

    public function setToken(?string $accessToken = null): void
    {
        if (null !== $accessToken) {
            $this->_token = $accessToken;
        }
    }
    // @codeCoverageIgnoreEnd

    public function post(string $url, array $data = []): Response
    {
        return call_user_func($this->oResponse, $this->apiCall($url, $data));
    }

    public function get(string $url, array $data = []): Response
    {
        return call_user_func($this->oResponse, $this->apiCall($url, $data, 'get'));
    }

    // @codeCoverageIgnoreStart
    protected function apiCall(string $url, array $data = [], string $type = 'POST'): array
    {
        $url = $this->buildUri($url);
        $crl = curl_init($url);
        $header = [];
        $header[] = 'Content-type: application/json';
        if (null !== $this->getToken()) {
            $header[] = 'Authorization: Bearer ' . $this->getToken();
        }
        curl_setopt($crl, CURLOPT_CUSTOMREQUEST, strtoupper($type));

        curl_setopt($crl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($crl, CURLOPT_HTTPHEADER, $header);

        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_SSLVERSION, 6);
        curl_setopt($crl, CURLOPT_TIMEOUT, 30);
        $rest = curl_exec($crl);
        $_code = curl_getinfo($crl, CURLINFO_HTTP_CODE);
        curl_close($crl);
        $_rest = $rest;

        return ['result' => $_rest, 'code' => $_code, 'entity' => $this->_entity];
    }

    // @codeCoverageIgnoreStart
    protected function getToken(): ?string
    {
        return $this->_token;
    }

    protected function _isCurl(): bool
    {
        return function_exists('curl_version');
    }

    private function buildUri(string $path): string
    {
        return sprintf(
            '%s/%s',
            rtrim(self::API_ENDPOINT, '/'),
            ltrim($path, '/')
        );
    }
}
