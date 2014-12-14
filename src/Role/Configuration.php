<?php


namespace Hshn\SerializerExtraBundle\Role;


/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class Configuration
{
    /**
     * @var array
     */
    private $attributes;

    /**
     * @var int
     */
    private $maxDepth;

    /**
     * @param array $attributes
     * @param int   $maxDepth
     */
    public function __construct(array $attributes = [], $maxDepth = 0)
    {
        $this->attributes = $attributes;
        $this->maxDepth = $maxDepth;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return int
     */
    public function getMaxDepth()
    {
        return $this->maxDepth;
    }
}
