[![N|Solid](https://www.camoo.hosting/img/logos/logoDomain.png)](https://www.camoo.hosting)

CAMOO SARL is an ANTIC Accredited Registrar in Cameroon.
You can use our API to check the availabiliity or to register cameroonian domain name extensions such as .CM, .CO.CM, .EDU.CM, .NET.CM or .COM.CM

This library sends authenticated requests to Camoo.Hosting. You can check our
[API documentation](https://api-doc.camoo.hosting) for available modules and
operations.

Requirement
-----------
CAMOO.HOSTING API client for PHP requires PHP 8.1 or newer.

Example
-------
### 1. Install the package

```shell
composer require camoo/hosting
```

The package requires PHP 8.1 or newer and needs the `curl` and `openssl`
extensions enabled.

### 2. Configure credentials

The client reads credentials from the process environment. For a local shell,
export them before starting PHP:

```shell
export CAMOO_HOSTING_EMAIL="you@example.com"
export CAMOO_HOSTING_PASSWORD="your-password"
export ACCESS_TOKEN_SALT="generate-a-long-random-secret"
```

For an application using dotenv, put the same values in its untracked `.env`
file and load that file before creating a module. Do not commit credentials to
Git. `ACCESS_TOKEN_SALT` encrypts the local token cache; use a different random
value per installation and keep it stable between deployments.

### 3. Make a request

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Camoo\Hosting\Modules\Domains;

$domains = new Domains();
$response = $domains->checkAvailability('example', 'cm');

if ($response->getStatusCode() !== 200) {
    throw new RuntimeException('Camoo.Hosting request failed: ' . $response->getError());
}

var_dump($response->getJson());
```

The client reads `CAMOO_HOSTING_EMAIL` and `CAMOO_HOSTING_PASSWORD` when a
module needs authentication. This keeps secrets out of PHP source. In a
dotenv-based application, put them in the untracked `.env` file:

```dotenv
CAMOO_HOSTING_EMAIL="you@example.com"
CAMOO_HOSTING_PASSWORD="your-password"
ACCESS_TOKEN_SALT="a-long-random-secret"
```

Never commit this file or place credentials in templates, controllers, or
example source code.

Run the example from the project directory after exporting the variables:

```shell
php exmaple.php
```

For integrations that receive credentials from a secret manager instead of the
environment, credentials can be supplied directly:

```php
$token = (new \Camoo\Hosting\Lib\AccessToken())->get([
    'email' => $email,
    'password' => $password,
]);
```

If credentials are missing, the client throws an actionable
`AccessTokenException` instead of making an unauthenticated request.

Resources
---------

  * [Documentation](https://api-doc.camoo.hosting)
  * [Report issues](https://github.com/camoo/hosting/issues)
