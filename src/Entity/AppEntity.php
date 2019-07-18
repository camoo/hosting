<?php

namespace Camoo\Hosting\Entity;

/**
 * Class AppEntity
 * @author CamooSarl
 */
class AppEntity
{
    public function get($obj)
    {
        $sourceReflection = new \ReflectionObject($obj);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $name = $sourceProperty->getName();
            $this->{$name} = $obj->$name;
        }
        return $this;
    }

    public function convert($array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->$key = new \stdClass();
                $this->convert($value, $this->$key);
            } else {
                $this->$key = $value;
            }
        }
        return $this;
    }
}
