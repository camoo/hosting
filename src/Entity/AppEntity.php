<?php

declare(strict_types=1);

namespace Camoo\Hosting\Entity;

use Camoo\Hosting\Factory\EntityFactory;
use ReflectionObject;
use stdClass;

/**
 * Class AppEntity
 *
 * @author CamooSarl
 */
class AppEntity implements EntityInterface
{
    /** @var array|string[] */
    private static array $asMapping = ['result', 'price', 'promo'];

    /**
     * @param array<int,mixed> $arguments
     *
     * @return void|null
     */
    public function __call(string $name, array $arguments)
    {
        $action = substr($name, 0, 3);
        switch ($action) {
            case 'get':
                $property = strtolower(substr($name, 3));

                return $this->get($property);
            case 'set':
                $property = strtolower(substr($name, 3));

                $this->set($property, $arguments[0]);
                break;
            default:
                return null;
        }
    }

    public function has(string $property): bool
    {
        return property_exists($this, $property);
    }

    public function get(string $property): mixed
    {
        return $this->has($property) ? $this->{$property} : null;
    }

    /** @inheritDoc */
    public function set(array|string $data, mixed $value = null): void
    {
        if (is_array($data)) {
            foreach ($data as $property => $val) {
                if ($this->has($property)) {
                    $this->{$property} = $val;
                }
            }
        } elseif ($this->has($data)) {
            $this->{$data} = $value;
        }
    }

    public function convert(mixed $data): ?self
    {
        if (is_array($data)) {
            return $this->convertArray($data);
        }
        if (is_object($data)) {
            return $this->convertObj($data);
        }

        return null;
    }

    /** @return array|string[] */
    private function getMapping(): array
    {
        return self::$asMapping;
    }

    private function convertObj(object $obj): self
    {
        $sourceReflection = new ReflectionObject($obj);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $name = $sourceProperty->getName();
            $normalizedName = $this->normalizeKey($name);
            if (is_object($obj->$name)) {
                $class = clone $this->determineClass('');
                if (in_array($name, $this->getMapping())) {
                    $class = clone $this->determineClass(ucfirst($name));
                }

                $this->{$normalizedName} = $class->convert($obj->$name);
            } else {

                $this->{$normalizedName} = $obj->{$name};
            }
        }

        return $this;
    }

    private function determineClass(string $propertyName): EntityInterface
    {
        return EntityFactory::create()->getEntityClass($propertyName);
    }

    /**
     * @param array<string, mixed> $array
     */
    private function convertArray(array $array): self
    {
        foreach ($array as $key => $value) {
            $normalizedName = $this->normalizeKey($key);
            if (is_array($value)) {
                $this->{$normalizedName} = new stdClass();
                $this->convert($value);
            } else {
                $this->{$normalizedName} = $value;
            }
        }

        return $this;
    }

    private function normalizeKey(string $key): string
    {
        return str_replace('-', '_', $key);
    }
}
