<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\SolrAnnotations\Test\Unit\Schema;

use Refinery29\SolrAnnotations\Schema\Schema;
use Refinery29\Test\Util\Faker\GeneratorTrait;

class SchemaTest extends \PHPUnit_Framework_TestCase
{
    use GeneratorTrait;

    public function testCanGetSetValues()
    {
        $faker = $this->getFaker();

        $one = $faker->word;
        $two = $faker->word;
        $three = $faker->word;

        $name = $faker->word;

        $fields = [$one, $two, $three];

        $schema = new Schema($name, $fields);

        $this->assertSame($schema->getName(), $name);
        $this->assertSame($schema->getFields(), $fields);
        $this->assertCount(3, $schema->getFields());
    }
}
