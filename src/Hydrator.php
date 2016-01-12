<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\SolrAnnotations;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Refinery29\SolrAnnotations\Annotation\Document as DocumentAnnotation;
use Refinery29\SolrAnnotations\Annotation\Field;
use ReflectionClass;

class Hydrator
{
    /**
     * @param AnnotationReader $annotationReader
     */
    public function __construct(AnnotationReader $annotationReader = null)
    {
        self::registerAnnotations();

        $this->reader = $annotationReader ?: new AnnotationReader();
    }

    /**
     * @param $object
     * @param $document
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function hydrate($object, $document)
    {
        $reflClass = new ReflectionClass($object);
        $this->validateClassAnnotation($reflClass);

        $propertyAnnotations = $this->parsePropertyAnnotations($reflClass);

        $document = (array) json_decode($document);
        $hydrated = new $object();

        foreach ($document as $field => $value) {
            if ($this->propertyCanBeSet($hydrated, $field, $propertyAnnotations)) {
                $setter = $this->getSetterMethod($propertyAnnotations[$field]);
                $hydrated->$setter($value);
            }
        }

        return $hydrated;
    }

    /**
     * @param $class
     * @param $property
     * @param $propertyAnnotations
     *
     * @return bool
     */
    private function propertyCanBeSet($class, $property, $propertyAnnotations)
    {
        if (array_key_exists($property, $propertyAnnotations)) {
            $setter = $this->getSetterMethod($propertyAnnotations[$property]);
            if (method_exists($class, $setter)) {
                return true;
            }
        }
    }

    /**
     * @param $property
     *
     * @return string
     */
    private function getSetterMethod($property)
    {
        return 'set' . ucfirst($property);
    }

    /**
     * @param ReflectionClass $class
     *
     * @return array
     */
    private function parsePropertyAnnotations(ReflectionClass $class)
    {
        $properties = $class->getProperties();

        $propertyAnnotations = [];
        foreach ($properties as $property) {
            $annotationName = $this->reader
                ->getPropertyAnnotation($property, Field::class)
                ->getName();

            $propertyAnnotations[$property->getName()] = $annotationName;
        }

        return array_flip($propertyAnnotations);
    }

    /**
     * @param ReflectionClass $class
     *
     * @throws \Exception
     */
    private function validateClassAnnotation(ReflectionClass $class)
    {
        $classAnnotation = $this->reader->getClassAnnotation($class, DocumentAnnotation::class);

        if (empty($classAnnotation)) {
            throw new \Exception(DocumentAnnotation::class . ' is required');
        }
    }

    public static function registerAnnotations()
    {
        AnnotationRegistry::registerFile(__DIR__ . '/Annotation/Field.php');
        AnnotationRegistry::registerFile(__DIR__ . '/Annotation/Document.php');
        AnnotationRegistry::registerFile(__DIR__ . '/Annotation/ExtraSchema.php');
    }
}
