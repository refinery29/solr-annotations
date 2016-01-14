<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\SolrAnnotations\Schema;

use Refinery29\SolrAnnotations\Annotation\Parser;
use ReflectionClass;

class SchemaBuilder
{
    /**
     * @param Parser $parser
     */
    public function __construct(Parser $parser = null)
    {
        $this->parser = $parser ?: new Parser();
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

        $name = $this->parser->getDocumentName($reflClass);

        $fields = $this->parser->getProperties($reflClass);
        $fields = array_merge($fields, $this->parser->getExtraSchema($reflClass));

        return new Schema($name, $fields);
    }
}
