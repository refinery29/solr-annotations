<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\SolrAnnotations\Test\Unit\Annotation;

use Refinery29\SolrAnnotations\Annotation\Parser;
use Refinery29\SolrAnnotations\Test\Unit\Stub\AnnotatedClass;
use Refinery29\SolrAnnotations\Test\Unit\Stub\NonAnnotatedClass;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testItWillThrowExceptionWithoutDocumentAnnotation()
    {
        $this->setExpectedException(\Exception::class);

        $parser = new Parser();

        $refl = new \ReflectionClass(NonAnnotatedClass::class);

        /* @var AnnotatedClass $annotatedClass */
        $parser->validateClassAnnotation($refl);
    }

    public function tesItCanGetDocumentName()
    {
        $refl = new \ReflectionClass(AnnotatedClass::class);

        $parser = new Parser();

        $name = $parser->getDocumentName($refl);

        $this->assertSame($name, 'AnnotatedClassDocument');
    }
}
