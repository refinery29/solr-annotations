<?php

namespace Refinery29\SolrAnnotations\Test\Unit;

use Refinery29\SolrAnnotations\Hydrator;
use Refinery29\SolrAnnotations\Test\Unit\Stub\AnnotatedClass;
use Refinery29\SolrAnnotations\Test\Unit\Stub\NonAnnotatedClass;
use Refinery29\Test\Util\Faker\GeneratorTrait;
use Refinery29\Test\Util\PHPUnit\BuildsMockTrait;

class HydratorTest extends \PHPUnit_Framework_TestCase
{
    use GeneratorTrait;
    use BuildsMockTrait;

    public function testItCanHydrateAnnotatedClass()
    {
        $faker = $this->getFaker();
        $name = $faker->word;
        $email = $faker->email;
        $age = $faker->randomDigit;

        $document = json_encode(
            [
                'name_s' => $name,
                'age_i' => $age,
                'email_s' => $email,
            ]
        );

        $hydrator = new Hydrator();

        /** @var AnnotatedClass $annotatedClass */
        $annotatedClass = $hydrator->hydrate(AnnotatedClass::class, $document);

        $this->assertSame($annotatedClass->getName(), $name);
        $this->assertSame($annotatedClass->getEmail(), $email);
        $this->assertSame($annotatedClass->getAge(), $age);
    }

    public function testItWillSkipDocumentFieldsWithNoSetter()
    {
        $faker = $this->getFaker();
        $name = $faker->word;
        $email = $faker->email;
        $age = $faker->randomDigit;
        $document = json_encode(
            [
                'name_s' => $name,
                'age_i' => $age,
                'email_s' => $email,
                'title_s' => $faker->word,
            ]
        );

        $hydrator = new Hydrator();

        /** @var AnnotatedClass $annotatedClass */
        $annotatedClass = $hydrator->hydrate(AnnotatedClass::class, $document);

        $this->assertSame($annotatedClass->getName(), $name);
        $this->assertSame($annotatedClass->getEmail(), $email);
        $this->assertSame($annotatedClass->getAge(), $age);
    }

    public function testItWillThrowExceptionWithoutDocumentAnnotation()
    {
        $this->setExpectedException(\Exception::class);

        $hydrator = new Hydrator();

        /* @var AnnotatedClass $annotatedClass */
        $hydrator->hydrate(NonAnnotatedClass::class, '');
    }
}
