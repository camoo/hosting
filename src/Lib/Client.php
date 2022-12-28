<?php

declare(strict_types=1);

namespace Camoo\Hosting\Lib;

use Camoo\Http\Curl\Domain\Entity\Configuration;
use Camoo\Http\Curl\Domain\Response\ResponseInterface;
use Camoo\Http\Curl\Infrastructure\Request;

class Client
{
    private const API_ENDPOINT = 'https://api.camoo.hosting/v1/';

    public function __construct(private ?string $accessToken = null, private ?string $entity = null)
    {
    }
    // @codeCoverageIgnoreEnd

    public function setToken(?string $accessToken = null): void
    {
        if (null === $accessToken) {
            return;
        }
        $this->accessToken = $accessToken;
    }
    // @codeCoverageIgnoreEnd

    public function post(string $url, array $data = []): Response
    {
        return new Response($this->apiCall($url, $data), $this->entity);
    }

    public function get(string $url, array $data = []): Response
    {
        return new Response($this->apiCall($url, $data, 'GET'), $this->entity);
    }

    // @codeCoverageIgnoreStart
    protected function apiCall(string $url, array $data = [], string $type = 'POST'): ResponseInterface
    {
        $url = $this->buildUri($url);
        $header = ['Content-type' => 'multipart/form-data'];

        if (null !== $this->getToken()) {
            $header['Authorization'] = 'Bearer ' . $this->getToken();
        }

        $client = new \Camoo\Http\Curl\Infrastructure\Client();
        $request = new Request(Configuration::create(), $url, $header, $data, $type);

        return $client->sendRequest($request);
    }

    // @codeCoverageIgnoreStart
    protected function getToken(): ?string
    {
        return $this->accessToken;
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
