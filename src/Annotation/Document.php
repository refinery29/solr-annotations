<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

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
