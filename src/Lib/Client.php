<?php

declare(strict_types=1);

namespace Camoo\Hosting\Lib;

use Camoo\Hosting\Exception\ClientException;

class Client
{
    private const API_ENDPOINT = 'https://api.camoo.hosting/v1/';

    protected array $oResponse = [Response::class, 'create'];

    private ?string $token = null;

    private ?string $entity = null;

    public function __construct(?string $accessToken = null, ?string $entity = null)
    {
        if (!$this->hasCurlSupport()) {
            throw new ClientException('PHP-Curl module is missing!', E_USER_ERROR);
        }

        if (null !== $accessToken) {
            $this->token = $accessToken;
        }
        if (null !== $entity) {
            $this->entity = $entity;
        }
    }
    // @codeCoverageIgnoreEnd

    public function setToken(?string $accessToken = null): void
    {
        if (null !== $accessToken) {
            $this->token = $accessToken;
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
        $code = curl_getinfo($crl, CURLINFO_HTTP_CODE);
        curl_close($crl);

        return ['result' => $rest, 'code' => $code, 'entity' => $this->entity];
    }

    // @codeCoverageIgnoreStart
    protected function getToken(): ?string
    {
        return $this->token;
    }

    protected function hasCurlSupport(): bool
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
