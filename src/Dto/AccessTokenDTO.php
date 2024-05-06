<?php

declare(strict_types=1);

namespace Camoo\Hosting\Dto;

use Stringable;

final class AccessTokenDTO implements Stringable
{
    public function __construct(
        public string $accessToken,
        public string $tokenType,
        public int $expiresIn,
        public int $issuedAt,
        public string $scope,
    ) {
    }

    public function __toString(): string
    {
        return $this->accessToken;
    }
}
