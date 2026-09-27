<?php

declare(strict_types=1);

namespace Camoo\Hosting\Entity;

/**
 * Class Configuration
 *
 * @author CamooSarl
 */
final class Configuration extends AppEntity
{
    /** ISO-style application locale selected by the reseller. */
    public ?string $locale = null;
}
