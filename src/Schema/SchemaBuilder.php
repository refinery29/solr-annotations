<?php

namespace Refinery29\SolrAnnotations\Schema;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Refinery29\SolrAnnotations\Annotation\Document;
use Refinery29\SolrAnnotations\Annotation\ExtraSchema;
use Refinery29\SolrAnnotations\Annotation\Field;
use ReflectionClass;

class SchemaBuilder
{
    /**
     * @param AnnotationReader $annotationReader
     */
    public function __construct(AnnotationReader $annotationReader = null)
    {
        AnnotationRegistry::registerFile(__DIR__ . '/../Annotation/Field.php');
        AnnotationRegistry::registerFile(__DIR__ . '/../Annotation/Document.php');
        AnnotationRegistry::registerFile(__DIR__ . '/../Annotation/ExtraSchema.php');

        $this->reader = $annotationReader ?: new AnnotationReader();
    }

    /**
     * @param $object
     *
     * @throws \Exception
     *
     * @return array
     */
    public function buildSchema($object)
    {
        $reflClass = new ReflectionClass($object);

        $name = $this->getDocumentNameAnnotation($reflClass);

        $fields = $this->parsePropertyFields($reflClass);
        $fields = array_merge($fields, $this->parseExtraSchema($reflClass));

        return new Schema($name, $fields);
    }

    /**
     * @param ReflectionClass $class
     *
     * @return array
     */
    private function parseExtraSchema(ReflectionClass $class)
    {
        $fields = $this->reader
            ->getClassAnnotation($class, ExtraSchema::class)
            ->getFields();

        $returnFields = [];

        if ($fields) {
            foreach ($fields as $field) {
                $returnFields[] = $field;
            }
        }

        return $returnFields;
    }

    /**
     * @param ReflectionClass $class
     *
     * @return array
     */
    private function parsePropertyFields(ReflectionClass $class)
    {
        $properties = $class->getProperties();

        $fields = [];

        foreach ($properties as $property) {
            $fields[] = $this->reader->getPropertyAnnotation($property, Field::class);
        }

        return $fields;
    }

    /**
     * @param ReflectionClass $class
     *
     * @throws \Exception
     *
     * @return null|object
     */
    private function getDocumentNameAnnotation(ReflectionClass $class)
    {
        $classAnnotation = $this->reader->getClassAnnotation($class, Document::class);

        if (empty($classAnnotation)) {
            throw new \Exception(Document::class . ' is required');
        }

        return $classAnnotation->getName();
    }
}
