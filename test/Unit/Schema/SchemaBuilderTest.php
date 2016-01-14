<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\SolrAnnotations\Test\Unit\Schema;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Refinery29\SolrAnnotations\Annotation\Field;
use Refinery29\SolrAnnotations\Schema\Schema;
use Refinery29\SolrAnnotations\Schema\SchemaBuilder;
use Refinery29\SolrAnnotations\Test\Unit\Stub\AnnotatedClass;
use Refinery29\Test\Util\Faker\GeneratorTrait;

class SchemaBuilderTest extends \PHPUnit_Framework_TestCase
{
    use GeneratorTrait;

    public function setup()
    {
        AnnotationRegistry::registerAutoloadNamespace('Refinery29/SolrAnnotations/Annotation');
    }

    public function testCanBuildSchema()
    {
        $builder = new SchemaBuilder();

        /** @var Schema $schema */
        $schema = $builder->buildSchema(AnnotatedClass::class);

        $this->assertSame($schema->getName(), 'AnnotatedClassDocument');

        /* @var Field[] $fields */
        $fieldNames = array_keys($schema->getFields());

        $this->assertCount(5, $fieldNames);

        $this->assertSame('name_s', array_shift($fieldNames));
        $this->assertSame('email_s', array_shift($fieldNames));
        $this->assertSame('age_i', array_shift($fieldNames));
        $this->assertSame('has_something_i', array_shift($fieldNames));
        $this->assertSame('tags', array_shift($fieldNames));
    }
}
