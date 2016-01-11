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
use Symfony\Component\Config\Definition\Exception\Exception;

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

        $propertyTypes = $this->getPropertyType($reflClass);

        $document = (array) json_decode($document);

        $hydrated = new $object();

        foreach ($document as $field => $value) {
            if (is_array($value)) {
                continue;
            }

            if ($this->propertyCanBeSet($hydrated, $field, $propertyAnnotations)) {
                $field = $propertyAnnotations[$field];
                $type = $propertyTypes[$field];
                $value = $this->coerceValue($type, $value);
                $setter = $this->getSetterMethod($field);

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
        if ($property == 'id') {
            return false;
        }

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

    /**
     * @param ReflectionClass $class
     *
     * @return mixed
     */
    private function getPropertyType(\ReflectionClass $class)
    {
        $properties = $class->getProperties();

        $propertyTypes = [];
        foreach ($properties as $property) {
            $propertyDoc = $property->getDocComment();
            $propertyType = 'string';

            if (strpos($propertyDoc, '@var ') !== false) {
                $length = strlen($propertyDoc);
                $varDefinition = substr($propertyDoc, strpos($propertyDoc, '@var'), $length - 1);

                $propertyType = trim(trim($varDefinition, '@var\n/*'));
            }

            $propertyName = $property->getName();
            $propertyTypes[$propertyName] = $propertyType;
        }

        return $propertyTypes;
    }

    public function toString($val)
    {
        return (string) $val;
    }

    public function toBool($val)
    {
        if ($val === true || $val === false) {
            return $val;
        }

        if ($val === 0) {
            return false;
        } elseif ($val === 1) {
            return true;
        }

        throw new Exception('Invalid Boolean Value Provided');
    }

    public function toInt($val)
    {
        return (int) $val;
    }

    private function coerceValue($type, $value)
    {
        switch ($type) {
            case 'string':
                $value = $this->toString($value);
                break;
            case 'bool':
                $value = $this->toBool($value);
                break;
            case 'int':
                $value = $this->toInt($value);
                break;
            default:
                $value = $this->toString($value);
                break;
        };

        return $value;
    }
}
