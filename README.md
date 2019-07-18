[![N|Solid](https://www.camoo.hosting/img/logos/logoDomain.png)](https://www.camoo.hosting)

CAMOO SARL is an ANTIC Accredited Registrar in Cameroon.
You can use our API to check the availabiliity or to register cameroonian domain name extensions such as .CM, .CO.CM, .EDU.CM, .NET.CM or .COM.CM

This is only an example library, that can be used to send API request to camoo.hosting! You can check our [Documentation](https://api-doc.camoo.hosting) for more.

Requirement
-----------
CAMOO.HOSTING API client for PHP requires version 5.6 and above

Example
-------
```php
use Camoo\Hosting\Modules\Domains;
 
 // set your token salt for the local cache.
 // you can visit this link https://api.wordpress.org/secret-key/1.1/salt/ to pickup one
 define('ACCESS_TOKEN_SALT', 'Your super secret Key here');
 
 // SET your CAMOO.HOSTING creadentials
 define('cm_email', 'you@gmail.com');
 define('cm_passwd', '2BSe3@pMRbCnV>J(G');
 
 $oDomain = new Domains();
 $oResponse = $oDomain->checkAvailability('example', 'cm');
 // Get Entity
 var_dump($oResponse->getEntity());

 // OR get Array instead of entity
 var_dump($oResponse->getJson());
```

Resources
---------

  * [Documentation](https://api-doc.camoo.hosting)
  * [Report issues](https://github.com/camoo/hosting/issues)
