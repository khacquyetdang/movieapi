<?php

namespace App\Entity;

use Doctrine\Common\Annotations\Reader;

class EntityMerger
{
    private $annotationReader;

    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * @param $entity
     * @param $changes
     */
    public function merge($entity, $changes): void
    {
        $entityClassName = \get_class($entity);
        if (false === $entityClassName) {
            throw new InvalidArgumentException("$entity is not a valid class");
        }

        $changesClassName = \get_class($changes);
        if (false === $changesClassName) {
            throw new InvalidArgumentException("$changes is not a valid class");
        }

        if (!is_a($changes, $entityClassName)) {
            throw new InvalidArgumentException("Cannot merge object of class $changesClassName with object of $entityClassName");
        }

        $entityReflection = new \ReflectionObject($entity);
        $changesReflection = new \ReflectionObject($changes);

        foreach ($changesReflection->getProperties() as $changedProperty) {
            $changedProperty->setAccessible(true);
            $changedPropertyValue = $changedProperty->getValue($changes);

            // Ignore $changes property with null value
            if (null === $changedPropertyValue) {
                continue;
            }

            // Ignore $changes property if it's not present on $entity
            if (!$entityReflection->hasProperty($changedProperty->getName())) {
                continue;
            }

            $entityProperty = $entityReflection->getProperty($changedProperty->getName());
            $annotation = $this->annotationReader->getPropertyAnnotation($entityProperty, Id::class);

            // Ignore $changes property that has Doctrine @Id annotation
            if (null !== $annotation) {
                continue;
            }

            $entityProperty->setAccessible(true);
            $entityProperty->setValue($entity, $changedPropertyValue);
        }
    }

    /**
     * Get the value of annotationReader
     */
    public function getAnnotationReader()
    {
        return $this->annotationReader;
    }

    /**
     * Set the value of annotationReader
     *
     * @return  self
     */
    public function setAnnotationReader($annotationReader)
    {
        $this->annotationReader = $annotationReader;

        return $this;
    }
}
