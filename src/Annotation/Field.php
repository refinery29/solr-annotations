<?php

namespace Refinery29\SolrAnnotations\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Attributes({
 *   @Attribute("name",  type = "string")
 * })
 */
class Field
{
    /**
     * @var string
     * @Required
     */
    private $name;

    public function __construct(array $values)
    {
        $this->name = $values['name'];
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}
