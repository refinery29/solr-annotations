<?php

namespace Refinery29\SolrAnnotations\Test\Unit\Schema;

use Refinery29\SolrAnnotations\Annotation\Field;
use Refinery29\SolrAnnotations\Schema\Schema;
use Refinery29\SolrAnnotations\Schema\SchemaBuilder;
use Refinery29\SolrAnnotations\Test\Unit\Stub\AnnotatedClass;
use Refinery29\Test\Util\Faker\GeneratorTrait;

class SchemaBuilderTest extends \PHPUnit_Framework_TestCase
{
    use GeneratorTrait;

    public function testCanBuildSchema()
    {
        $builder = new SchemaBuilder();

        /** @var Schema $schema */
        $schema = $builder->buildSchema(AnnotatedClass::class);

        $this->assertSame($schema->getName(), 'AnnotatedClassDocument');

        /** @var Field[] $fields */
        $fields = $schema->getFields();

        $this->assertCount(4, $fields);

        $this->assertSame('name_s', $fields[0]->getName());
        $this->assertSame('email_s', $fields[1]->getName());
        $this->assertSame('age_i', $fields[2]->getName());
    }
}
