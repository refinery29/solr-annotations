<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\SolrAnnotations\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Refinery29\SolrAnnotations\Annotation\Document as DocumentAnnotation;
use ReflectionClass;

/**
 * Given an annotated class the Parser reads over the file to determine available search fields,
 * document name, etc.
 */
class Parser
{
    /**
     * @var AnnotationReader
     */
    private $reader;

    /**
     * @param AnnotationReader $reader
     */
    public function __construct(AnnotationReader $reader = null)
    {
        self::registerAnnotations();
        $this->reader = $reader ?: new AnnotationReader();
    }

    /**
     * @param ReflectionClass $class
     *
     * Validates that the Document annotation is present. This is required for the
     * Annotated class to be considered valid.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function validateClassAnnotation(ReflectionClass $class)
    {
        /** @var DocumentAnnotation $classAnnotation */
        $classAnnotation = $this->reader->getClassAnnotation($class, DocumentAnnotation::class);

        if (empty($classAnnotation)) {
            throw new \Exception(DocumentAnnotation::class . ' is required');
        }

        return $classAnnotation;
    }

    /**
     * @param ReflectionClass $class
     *
     * Validates and returns the Document annotation on a class
     *
     * @throws \Exception
     *
     * @return null|object
     */
    public function getDocumentName(ReflectionClass $class)
    {
        return $this->validateClassAnnotation($class)->getName();
    }

    /**
     * @param ReflectionClass $class
     *
     * @return array
     */
    public function getProperties(ReflectionClass $class)
    {
        $properties = $class->getProperties();

        $propertyAnnotations = [];
        foreach ($properties as $property) {
            $annotationName = $this->reader
                ->getPropertyAnnotation($property, Field::class);

            if ($annotationName) {
                $annotationName = $annotationName->getName();
                $propertyAnnotations[$property->getName()] = $annotationName;
            }
        }

        return array_flip($propertyAnnotations);
    }

    /**
     * @param ReflectionClass $class
     *
     * @return array
     */
    public function getPropertyTypes(\ReflectionClass $class)
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

    /**
     * @param ReflectionClass $class
     *
     * @return array
     */
    public function getExtraSchema(ReflectionClass $class)
    {
        $fields = $this->reader
            ->getClassAnnotation($class, ExtraSchema::class)
            ->getFields();

        $returnFields = [];

        if ($fields) {
            foreach ($fields as $field) {
                $returnFields[] = $field->getName();
            }
        }

        return array_flip($returnFields);
    }

    public function getAllSearchableFields(ReflectionClass $class)
    {
        return array_merge($this->getProperties($class), $this->getExtraSchema($class));
    }

    /**
     *
     */
    public static function registerAnnotations()
    {
        AnnotationRegistry::registerFile(__DIR__ . '/Field.php');
        AnnotationRegistry::registerFile(__DIR__ . '/Document.php');
        AnnotationRegistry::registerFile(__DIR__ . '/ExtraSchema.php');
    }
}
