<?php
declare(strict_types=1);

namespace Camoo\Hosting\Entity;

use ReflectionObject;
use stdClass;

/**
 * Class AppEntity
 * @author CamooSarl
 */
class AppEntity
{
    private static $asMapping = ['result','price','promo'];

    public function __call($name, $arguments)
    {
        $action = substr($name, 0, 3);
        switch ($action) {
                        case 'get':
                            $property = strtolower(substr($name, 3));
                            return $this->get($property);
                            break;
                        case 'set':
                            $property = strtolower(substr($name, 3));
                            return $this->set($property, $arguments[0]);
                            break;
                        default:
                            return null;
                    }
    }

    public function has(string $property) : bool
    {
        return property_exists($this, $property);
    }

    public function get(string $property)
    {
        if (isset($property) && !empty($property) && $this->has($property)) {
            return $this->{$property};
        }
        return null;
    }

    public function set($xData, $value=null)
    {
        if (is_array($xData)) {
            foreach ($xData as $property => $value) {
                if ($this->has($property)) {
                    $this->set($property, $value);
                }
            }
        } elseif (is_string($xData) && $this->has($xData)) {
            $data = [$xData => $value];
            $this->set($data);
        }
        return null;
    }

    public function convert($xData)
    {
        if (is_array($xData)) {
            return $this->convertArray($xData);
        }
        if (is_object($xData)) {
            return $this->convertObj($xData);
        }
    }

    private function getMapping() : array
    {
        return self::$asMapping;
    }

    private function convertObj($obj) : AppEntity
    {
        $sourceReflection = new ReflectionObject($obj);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $name = $sourceProperty->getName();
            if (is_object($obj->$name)) {
                $class = '\\Camoo\\Hosting\\Entity\\Content';
                if (in_array($name, $this->getMapping())) {
                    $class = '\\Camoo\\Hosting\\Entity\\' . ucfirst($name);
                }
                $this->{$name} = (new $class)->convert($obj->$name);
            } else {
                $this->{$name} = $obj->{$name};
            }
        }
        return $this;
    }

    private function convertArray(array $array) : AppEntity
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->{$key} = new stdClass();
                $this->convert($value, $this->{$key});
            } else {
                $this->{$key} = $value;
            }
        }
        return $this;
    }
}
