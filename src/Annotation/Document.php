<?php

namespace Refinery29\SolrAnnotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *   @Attribute("name", type = "string"),
 *   @Attribute("annotProperty",  type = "SomeAnnotationClass"),
 * })
 */
class Document
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
