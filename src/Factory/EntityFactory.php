<?php

declare(strict_types=1);

namespace Camoo\Hosting\Factory;

use Camoo\Hosting\Entity\Configuration;
use Camoo\Hosting\Entity\Contact;
use Camoo\Hosting\Entity\Content;
use Camoo\Hosting\Entity\Customer;
use Camoo\Hosting\Entity\Dns;
use Camoo\Hosting\Entity\Domain;
use Camoo\Hosting\Entity\EntityInterface;
use Camoo\Hosting\Entity\Order;
use Camoo\Hosting\Entity\Payment;
use Camoo\Hosting\Entity\Price;
use Camoo\Hosting\Entity\Promo;
use Camoo\Hosting\Entity\Result;
use Camoo\Hosting\Entity\SubDomain;
use Camoo\Hosting\Entity\Tariff;

class EntityFactory implements EntityFactoryInterface
{
    private static ?self $instance = null;

    public static function create(): self
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getEntityClass(string $entity): EntityInterface
    {
        return match ($entity) {
            'Contact' => new Contact(),
            'Configuration' => new Configuration(),
            'Customer' => new Customer(),
            'Dns', 'Dn' => new Dns(),
            'Domain' => new Domain(),
            'Order' => new Order(),
            'Payment' => new Payment(),
            'Price' => new Price(),
            'Promo' => new Promo(),
            'Result' => new Result(),
            'SubDomain' => new SubDomain(),
            'Tariff' => new Tariff(),
            default => new Content() // Default case if none match
        };
    }
}
