<?php


namespace Hshn\SerializerExtraBundle\Role;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class AttributeManager implements \IteratorAggregate
{
    /**
     * @var array
     */
    private $attributes;

    /**
     *
     */
    public function __construct()
    {
        $this->attributes = [];
    }

    /**
     * @param string $class
     * @param array  $attributes
     */
    public function addAttributes($class, array $attributes)
    {
        $newAttributes = $attributes;

        if (isset($this->attributes[$class])) {
            $newAttributes = $this->attributes[$class];
            foreach ($attributes as $attribute) {
                if (!in_array($attribute, $newAttributes, true)) {
                    $newAttributes[] = $attribute;
                }
            }
        }

        $this->attributes[$class] = $newAttributes;
    }

    /**
     * @param string $class
     *
     * @return array
     */
    public function getAttributes($class)
    {
        if (!isset($this->attributes[$class])) {
            throw new \InvalidArgumentException(sprintf('Unsupported class "%s"', $class));
        }

        return $this->attributes[$class];
    }

    /**
     * @return array
     */
    public function getClasses()
    {
        return array_keys($this->attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->attributes);
    }
}
