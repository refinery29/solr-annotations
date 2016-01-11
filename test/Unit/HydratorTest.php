<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\SolrAnnotations\Test\Unit;

use Refinery29\SolrAnnotations\Hydrator;
use Refinery29\SolrAnnotations\Test\Unit\Stub\AnnotatedClass;
use Refinery29\SolrAnnotations\Test\Unit\Stub\AnnotatedClassPrivateConstructor;
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

    public function testItCanCoerceBoolean()
    {
        $faker = $this->getFaker();
        $name = $faker->word;
        $email = $faker->email;
        $age = $faker->randomDigit;
        $hasSomething = 0;

        $document = json_encode(
            [
                'name_s' => $name,
                'age_i' => $age,
                'email_s' => $email,
                'title_s' => $faker->word,
                'has_something_i' => $hasSomething,
            ]
        );

        $hydrator = new Hydrator();

        /** @var AnnotatedClass $annotatedClass */
        $annotatedClass = $hydrator->hydrate(AnnotatedClass::class, $document);

        $this->assertSame($annotatedClass->getName(), $name);
        $this->assertSame($annotatedClass->getEmail(), $email);
        $this->assertSame($annotatedClass->getAge(), $age);
        $this->assertFalse($annotatedClass->getHasSomething());
    }

    public function testItThrowExceptionOnInvalidBooleanValue()
    {
        $this->setExpectedException(\Exception::class);

        $faker = $this->getFaker();
        $name = $faker->word;
        $email = $faker->email;
        $age = $faker->randomDigit;
        $hasSomething = $faker->word;

        $document = json_encode(
            [
                'name_s' => $name,
                'age_i' => $age,
                'email_s' => $email,
                'title_s' => $faker->word,
                'has_something_i' => $hasSomething,
            ]
        );

        $hydrator = new Hydrator();
        $hydrator->hydrate(AnnotatedClass::class, $document);
    }

    public function testItCanCoerceInteger()
    {
        $faker = $this->getFaker();
        $name = $faker->word;
        $email = $faker->email;
        $age = (string)$faker->randomDigit;
        $hasSomething = $faker->boolean();

        $document = json_encode(
            [
                'name_s' => $name,
                'age_i' => $age,
                'email_s' => $email,
                'title_s' => $faker->word,
                'has_something_i' => $hasSomething,
            ]
        );

        $hydrator = new Hydrator();

        /** @var AnnotatedClass $annotatedClass */
        $annotatedClass = $hydrator->hydrate(AnnotatedClass::class, $document);

        $this->assertSame($annotatedClass->getName(), $name);
        $this->assertSame($annotatedClass->getEmail(), $email);
        $this->assertSame($annotatedClass->getAge(), (int)$age);
        $this->assertSame($annotatedClass->getHasSomething(), $hasSomething);
    }

    public function testItCanCoerceString()
    {
        $faker = $this->getFaker();
        $name = $faker->randomDigit;
        $email = $faker->email;
        $age = $faker->randomDigit;
        $hasSomething = $faker->boolean();

        $document = json_encode(
            [
                'name_s' => $name,
                'age_i' => $age,
                'email_s' => $email,
                'title_s' => $faker->word,
                'has_something_i' => $hasSomething,
            ]
        );

        $hydrator = new Hydrator();

        /** @var AnnotatedClass $annotatedClass */
        $annotatedClass = $hydrator->hydrate(AnnotatedClass::class, $document);

        $this->assertSame($annotatedClass->getName(), (string)$name);
        $this->assertSame($annotatedClass->getEmail(), $email);
        $this->assertSame($annotatedClass->getAge(), $age);
        $this->assertSame($annotatedClass->getHasSomething(), $hasSomething);
    }

    public function testCoercsionDefaultsToString()
    {
        $faker = $this->getFaker();
        $name = $faker->word;
        $email = $faker->randomDigit;
        $age = $faker->randomDigit;
        $hasSomething = $faker->boolean();

        $document = json_encode(
            [
                'name_s' => $name,
                'age_i' => $age,
                'email_s' => $email,
                'title_s' => $faker->word,
                'has_something_i' => $hasSomething,
            ]
        );

        $hydrator = new Hydrator();

        /** @var AnnotatedClass $annotatedClass */
        $annotatedClass = $hydrator->hydrate(AnnotatedClass::class, $document);

        $this->assertSame($annotatedClass->getName(), $name);
        $this->assertSame($annotatedClass->getEmail(), (string)$email);
        $this->assertSame($annotatedClass->getAge(), $age);
        $this->assertSame($annotatedClass->getHasSomething(), $hasSomething);
    }

    public function testItCanHydrateWithPrivateConstructor()
    {
        $faker = $this->getFaker();
        $name = $faker->word;
        $email = $faker->word;
        $age = $faker->randomDigit;
        $hasSomething = $faker->boolean();

        $document = json_encode(
            [
                'name_s' => $name,
                'age_i' => $age,
                'email_s' => $email,
                'title_s' => $faker->word,
                'has_something_i' => $hasSomething,
            ]
        );

        $hydrator = new Hydrator();

        /** @var AnnotatedClass $annotatedClass */
        $annotatedClass = $hydrator->hydrate(AnnotatedClassPrivateConstructor::class, $document);

        $this->assertSame($annotatedClass->getName(), $name);
        $this->assertSame($annotatedClass->getEmail(), (string)$email);
        $this->assertSame($annotatedClass->getAge(), $age);
        $this->assertSame($annotatedClass->getHasSomething(), $hasSomething);
        $this->assertSame($annotatedClass->getHasSomething(), $hasSomething);
    }

    public function testItCanHydratePrivateId()
    {
        $faker = $this->getFaker();
        $name = $faker->word;
        $id = $faker->randomDigit;
        $email = $faker->word;
        $age = $faker->randomDigit;
        $hasSomething = $faker->boolean();

        $document = json_encode(
            [
                'id' => $id,
                'name_s' => $name,
                'age_i' => $age,
                'email_s' => $email,
                'title_s' => $faker->word,
                'has_something_i' => $hasSomething,
            ]
        );

        $hydrator = new Hydrator();

        /** @var AnnotatedClassPrivateConstructor $annotatedClass */
        $annotatedClass = $hydrator->hydrate(AnnotatedClassPrivateConstructor::class, $document);

        $this->assertSame($annotatedClass->getName(), $name);
        $this->assertSame($annotatedClass->getEmail(), (string)$email);
        $this->assertSame($annotatedClass->getAge(), $age);
        $this->assertSame($annotatedClass->getHasSomething(), $hasSomething);
        $this->assertSame($annotatedClass->getHasSomething(), $hasSomething);
        $this->assertSame($annotatedClass->getId(), $id);
    }
}
