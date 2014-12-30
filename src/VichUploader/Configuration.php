<?php

namespace Hshn\SerializerExtraBundle\VichUploader;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class Configuration
{
    /**
     * @var int
     */
    private $maxDepth;

    /**
     * @var string
     */
    private $class;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @param string $class
     * @param array  $attributes
     * @param int    $maxDepth
     */
    public function __construct($class, array $attributes = [], $maxDepth = -1)
    {
        $this->class = $class;
        $this->attributes = [];
        $this->maxDepth = $maxDepth;

        foreach ($attributes as $attribute => $alias) {
            $this->setAttribute($attributes, $alias);
        }
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return int
     */
    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

    /**
     * @param string $attribute
     * @param string $alias
     *
     * @return Configuration
     */
    public function setAttribute($attribute, $alias = null)
    {
        $this->attributes[$attribute] = $alias ?: $attribute;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $attribute
     *
     * @return string
     */
    public function getAlias($attribute)
    {
        if (array_key_exists($attribute, $this->attributes)) {
            return $this->attributes[$attribute];
        }

        throw new \LogicException('Invalid attribute "%s"');
    }
}
