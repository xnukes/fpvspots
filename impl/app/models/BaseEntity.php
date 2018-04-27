<?php
/**
 * Class BaseEntity.php , Last changed 20.1.17 23:20
 * This file is part of the drones-map
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\Models;

use \Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\ArrayHash;

/** @ORM\MappedSuperclass */
class BaseEntity extends \Kdyby\Doctrine\Entities\BaseEntity
{
    public function toArray()
    {
        $result = [];
        foreach ($this as $col => $var) {

            $result[$col] = $var;
        }

        return $result;
    }

	public function setEntityValues($values)
	{
		if(!$values instanceof \Nette\Utils\ArrayHash && !is_array($values))
		{
			throw new \Exception('Values must be type Array or (object) ArrayHash');
		}
		$reflection = new \ReflectionClass($this);
		$docReader = new \Doctrine\Common\Annotations\AnnotationReader();
		foreach ($reflection->getProperties(\ReflectionProperty::IS_PROTECTED) as $key => $property)
		{
			if (!$property->isStatic())
			{
				if (array_key_exists($property->getName(), $values))
				{
					$value = $values[$property->getName()];
					$methodName = 'set' . ucfirst($property->getName());
					if (method_exists($this, $methodName))
					{
						$this->$methodName($value);
					} else {
						$docInfos = $docReader->getPropertyAnnotations($reflection->getProperty($property->getName()));
						if(isset($docInfos[0])) {
							$docInfos[0] instanceof Column && $docInfos[0]->nullable && !$value ? $value = null : null;

							if ($docInfos[0] instanceof Column && $docInfos[0]->type == 'datetime' && $value)
							{
								$this->{$property->getName()} = new \DateTime($value);
							} else {
								$this->{$property->getName()} = $value;
							}
						}
					}
				}
			}
		}
	}
}