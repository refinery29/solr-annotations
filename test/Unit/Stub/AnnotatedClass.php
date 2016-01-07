<?php

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
     * ) */
    private $name;

    /**
     * @Solr\Field(name="email_s")
     */
    private $email;

    /**
     * @Solr\Field(name="age_i")
     */
    private $age;

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
     * @return mixed
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
}
