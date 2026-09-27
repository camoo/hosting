<?php

require_once 'vendor/autoload.php';
use Camoo\Hosting\Modules\Domains;

// Set these in the process environment instead of committing secrets:
// CAMOO_HOSTING_EMAIL=you@gmail.com
// CAMOO_HOSTING_PASSWORD=your-password
// ACCESS_TOKEN_SALT=a-long-random-secret

$oDomain = new Domains();
$oResponse = $oDomain->checkAvailability('example', 'cm');
// Get Entity
var_dump($oResponse->getEntity());

// OR get Array instead of entity
var_dump($oResponse->getJson());
