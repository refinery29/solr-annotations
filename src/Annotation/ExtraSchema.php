<?php

namespace Refinery29\SolrAnnotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *   @Attribute("fields",  type="array<Refinery29\SolrAnnotations\Annotation\Field>")
 * })
 */
class ExtraSchema
{
    /**
     * @var Field[]
     * @Required
     */
    private $fields;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->setFields($values);
    }

    private function setFields($values)
    {
        if (!isset($values['fields'])) {
            throw new \InvalidArgumentException('Extra Schema Annotation requires fields property, none given');
        }
        $this->fields = $values['fields'];
    }

    /**
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }
}
