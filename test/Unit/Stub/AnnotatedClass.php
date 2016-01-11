<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\SolrAnnotations\Test\Unit\Stub;

use Refinery29\SolrAnnotations\Annotation as Solr;

/**
 * @Solr\Document(name="AnnotatedClassDocument")
 * @Solr\ExtraSchema(
 * fields = {
 *      @Solr\Field(name="tags")
 * })
 */
class AnnotatedClass
{
    /** @Solr\Field(
     *      name="name_s"
     * )
     *
     * @var string
     */
    private $name;

    /**
     * @Solr\Field(name="email_s")
     */
    private $email;

    /**
     * @Solr\Field(name="age_i")
     *
     * @var int
     */
    private $age;

    /**
     * @Solr\Field(name="has_something_i")
     *
     * @var bool
     */
    private $hasSomething;

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param mixed $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param mixed $hasSomething
     */
    public function setHasSomething($hasSomething)
    {
        $this->hasSomething = $hasSomething;
    }

    /**
     * @return mixed
     */
    public function getHasSomething()
    {
        return $this->hasSomething;
    }
}
