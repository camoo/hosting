<?php
require_once('vendor/autoload.php');
use Camoo\Hosting\Modules\Domains;

// set your token salt for the local cache.
// you can visit this link https://api.wordpress.org/secret-key/1.1/salt/ to pickup one
define('ACCESS_TOKEN_SALT', 'Your super secret Key here');
define('cm_email', 'you@gmail.com');
define('cm_passwd', '2BSe3@pMRbCnV>J(G');

$oDomain = new Domains;
var_dump($oDomain->checkAvailability('example', 'cm'));
