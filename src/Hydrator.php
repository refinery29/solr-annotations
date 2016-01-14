<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\SolrAnnotations;

use Refinery29\SolrAnnotations\Annotation\Parser;
use ReflectionClass;

class Hydrator
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @param Parser $parser
     *
     * @internal param AnnotationReader $annotationReader
     */
    public function __construct(Parser $parser = null)
    {
        $this->parser = $parser ?: new Parser();
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

        $this->parser->validateClassAnnotation($reflClass);

        $propertyAnnotations = $this->parser->getProperties($reflClass);

        $propertyTypes = $this->parser->getPropertyTypes($reflClass);

        $document = (array) json_decode($document);

        if ($reflClass->hasMethod('__construct')) {
            $reflClass->getMethod('__construct')
                ->setAccessible(true);
        }

        $hydrated = $reflClass->newInstanceWithoutConstructor();

        foreach ($document as $field => $value) {
            if (is_array($value)) {
                $hydrated->$field = $value;
            }

            if ($field === 'id') {
                $idValue = explode('_', $value);
                $idValue = array_pop($idValue);
                $id = $reflClass->getProperty('id');
                $id->setAccessible(true);
                $type = $propertyTypes[$field];
                $idValue = $this->coerceValue($type, $idValue);

                $id->setValue($hydrated, $idValue);
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
     * @param $val
     *
     * @return string
     */
    public function toString($val)
    {
        return (string) $val;
    }

    /**
     * @param $val
     *
     * @throws \Exception
     *
     * @return bool
     */
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

        throw new \Exception('Invalid Boolean Value Provided');
    }

    /**
     * @param $val
     *
     * @return int
     */
    public function toInt($val)
    {
        return (int) $val;
    }

    /**
     * @param $type
     * @param $value
     *
     * @throws \Exception
     *
     * @return bool|int|string
     */
    private function coerceValue($type, $value)
    {
        switch ($type) {
            case 'string':
                $value = $this->toString($value);
                break;
            case 'bool':
            case 'boolean':
                $value = $this->toBool($value);
                break;
            case 'int':
            case 'integer':
                $value = $this->toInt($value);
                break;
            default:
                $value = $this->toString($value);
                break;
        };

        return $value;
    }
}
